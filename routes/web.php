<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('produtos', ProductController::class)
        ->parameters(['produtos' => 'product'])
        ->names('products');

    Route::resource('categorias', CategoryController::class)
        ->parameters(['categorias' => 'category'])
        ->only(['index', 'store', 'destroy'])
        ->names('categories');

    Route::get('movimentacoes', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('movimentacoes/nova', [StockMovementController::class, 'create'])->name('movements.create');
    Route::post('movimentacoes', [StockMovementController::class, 'store'])->name('movements.store');

    Route::prefix('relatorios')->name('reports.')->group(function () {
        Route::get('faturamento', [ReportController::class, 'sales'])->name('sales');
        Route::get('faturamento/pdf', [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('faturamento/excel', [ReportController::class, 'salesExcel'])->name('sales.excel');
        Route::get('estoque/excel', [ReportController::class, 'stockExcel'])->name('stock.excel');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
