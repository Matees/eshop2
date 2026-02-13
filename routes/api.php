<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::prefix('address')->name('address.')->group(function () {
    Route::get('cities', [AddressController::class, 'cities'])->name('cities');
    Route::get('streets', [AddressController::class, 'streets'])->name('streets');
    Route::get('addresses', [AddressController::class, 'addresses'])->name('addresses');
});
