<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::resource('products', ProductController::class);

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('{id}', [CartController::class, 'add'])->name('add')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('{id}', [CartController::class, 'remove'])->name('remove');
});
Route::resource('orders', OrderController::class);

Route::prefix('api/address')->name('address.')->group(function () {
    Route::get('cities', [AddressController::class, 'cities'])->name('cities');
    Route::get('streets', [AddressController::class, 'streets'])->name('streets');
    Route::get('addresses', [AddressController::class, 'addresses'])->name('addresses');
    Route::get('postcode', [AddressController::class, 'postcode'])->name('postcode');
});
