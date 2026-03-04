<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::put('/{transaction}', 'update')->whereUuid('transaction')->name('update')->scopeBindings();
    Route::delete('/{transaction}', 'destroy')->whereUuid('transaction')->name('destroy')->scopeBindings();
});
