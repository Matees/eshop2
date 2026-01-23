<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ProductController::class, 'index'])->name('home');

Route::resource('products', ProductController::class);

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('{id}', [CartController::class, 'add'])->name('add')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('{id}', [CartController::class, 'remove'])->name('remove');
});
