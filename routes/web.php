<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'create'])->name('login.create');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::resource('products', ProductController::class)->only(['index', 'show']);

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('{id}', [CartController::class, 'add'])->name('add')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('{id}', [CartController::class, 'remove'])->name('remove');
});
Route::resource('orders', OrderController::class)->middleware([HandlePrecognitiveRequests::class])->only(['create', 'store']);

Route::get('/orders/track/{order}', [OrderController::class, 'show'])->name('orders.track')->middleware('signed');
