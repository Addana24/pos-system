<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();
        
        return view('pos.index', compact('categories', 'products'));
    }
    
    public function getProducts(Request $request)
    {
        $categoryId = $request->category_id;
        $search = $request->search;
        
        $query = Product::where('is_active', true)->where('stock', '>', 0);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        $products = $query->get();
        
        return response()->json($products);
    }
    
    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card',
            'paid_amount' => 'required|numeric|min:0',
        ]);
        
        try {
            DB::beginTransaction();
            
            $items = $request->items;
            $totalAmount = 0;
            $taxAmount = 0;
            $discountAmount = 0;
            
            // Calculate total amount
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                
                if ($product->stock < $quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak cukup untuk produk {$product->name}"
                    ], 422);
                }
                
                $totalAmount += $product->price * $quantity;
            }
            
            // Calculate tax (10%)
            $taxAmount = $totalAmount * 0.1;
            
            // Calculate final amount
            $finalAmount = $totalAmount + $taxAmount - $discountAmount;
            
            // Calculate change
            $paidAmount = $request->paid_amount;
            $changeAmount = $paidAmount - $finalAmount;
            
            if ($changeAmount < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran kurang'
                ], 422);
            }
            
            // Create transaction
            $transaction = Transaction::create([
                'invoice_number' => 'INV-' . date('YmdHis') . '-' . Str::random(5),
                'user_id' => Auth::id() ?? 1,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'notes' => $request->notes ?? null,
            ]);
            
            // Create transaction items and update stock
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $unitPrice = $product->price;
                $discount = 0;
                $subtotal = $unitPrice * $quantity - $discount;
                
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                ]);
                
                // Update stock
                $product->stock -= $quantity;
                $product->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil',
                'transaction' => $transaction->load('items.product')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function receipt($id)
    {
        $transaction = Transaction::with('items.product', 'user')->findOrFail($id);
        
        return view('pos.receipt', compact('transaction'));
    }
}