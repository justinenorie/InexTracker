<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::controller(CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::put('/{category}', 'update')->whereUuid('category')->name('update')->scopeBindings();
    Route::delete('/{category}', 'destroy')->whereUuid('category')->name('destroy')->scopeBindings();
});
