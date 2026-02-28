<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('pos')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('pos.index');
    Route::get('/products', [PosController::class, 'getProducts'])->name('pos.products');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/receipt/{id}', [PosController::class, 'receipt'])->name('pos.receipt');
});
