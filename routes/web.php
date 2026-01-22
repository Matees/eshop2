<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::resource('products', ProductController::class);

Route::prefix('cart')->name('cart.')->group(function () {
    Route::post('add/{id}', [CartController::class, 'addItem'])->name('add')->middleware([HandlePrecognitiveRequests::class]);
});
