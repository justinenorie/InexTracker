<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/trash', 'trash')->name('trash');
    Route::post('/', 'store')->name('store');
    Route::put('/{transaction}', 'update')->whereUuid('transaction')->name('update')->scopeBindings();
    Route::delete('/{transaction}', 'destroy')->whereUuid('transaction')->name('destroy')->scopeBindings();
    Route::post('/{id}/restore', 'restore')->whereUuid('id')->name('restore');
    Route::delete('/{id}/force-delete', 'forceDelete')->whereUuid('id')->name('force-delete');
});
