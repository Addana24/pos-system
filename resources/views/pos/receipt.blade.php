<div class="receipt">
    <div class="text-center mb-4">
        <h4 class="mb-1">POS System</h4>
        <p class="mb-0">Invoice #{{ $transaction->invoice_number }}</p>
        <p class="mb-0">{{ $transaction->created_at->format('d M Y H:i') }}</p>
    </div>
    
    <div class="mb-4">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mb-4">
        <div class="d-flex justify-content-between mb-1">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span>Tax (10%):</span>
            <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
        </div>
        @if($transaction->discount_amount > 0)
        <div class="d-flex justify-content-between mb-1">
            <span>Discount:</span>
            <span>Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="d-flex justify-content-between mb-1 fw-bold">
            <span>Total:</span>
            <span>Rp {{ number_format($transaction->final_amount, 0, ',', '.') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span>Payment Method:</span>
            <span>{{ ucfirst($transaction->payment_method) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span>Paid Amount:</span>
            <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>
        @if($transaction->payment_method == 'cash')
        <div class="d-flex justify-content-between mb-1">
            <span>Change:</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>
    
    @if($transaction->notes)
    <div class="mb-4">
        <p class="mb-1"><strong>Notes:</strong></p>
        <p>{{ $transaction->notes }}</p>
    </div>
    @endif
    
    <div class="text-center mt-4">
        <p class="mb-0">Thank you for your purchase!</p>
    </div>
</div>