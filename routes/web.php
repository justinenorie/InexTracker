<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/data/export-transactions', [App\Http\Controllers\DataController::class, 'exportTransactionsCsv'])->name('transactions.export');

    require __DIR__ . '/settings.php';
    require __DIR__ . '/categories.php';
    require __DIR__ . '/transactions.php';
});
