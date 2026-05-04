<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RestockAnalysisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;

// Guest Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard untuk semua role yang login
    Route::get('/', [DashboardController::class, 'index']);

    // Detail Penjualan (Bisa diakses Admin & Kasir)
    Route::middleware(['role:admin,administrator'])->group(function () {
        Route::get('/sales/{stockMovement}', [SaleController::class, 'show'])->name('sales.show');
    });

    // Khusus Administrator (Kasir)
    Route::middleware(['role:administrator'])->group(function () {
        Route::get('/sales', [SaleController::class, 'index']);
        Route::post('/sales', [SaleController::class, 'store']);
        Route::get('/histori-sales', [SaleController::class, 'history']);
    });

    // Khusus Admin dan Gudang untuk Manajemen Data
    Route::middleware(['role:admin,gudang'])->group(function () {
        Route::get('/purchases', [PurchaseController::class, 'index']);
        Route::post('/purchases', [PurchaseController::class, 'store']);
        Route::get('/gudang', [App\Http\Controllers\GudangController::class, 'index']);
        
        // Master Data CRUD
        Route::resource('categories', App\Http\Controllers\CategoryController::class)->except(['create', 'show', 'edit', 'update']);
        Route::resource('suppliers', App\Http\Controllers\SupplierController::class)->except(['create', 'show', 'edit', 'update']);
        Route::resource('products', App\Http\Controllers\ProductController::class)->except(['show']);
    });

    // Khusus Admin (Manajer/Owner)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/analysis', [RestockAnalysisController::class, 'index']);
        Route::get('/riwayat', [SaleController::class, 'adminHistory']);
        Route::get('/riwayat/{stockMovement}/edit', [SaleController::class, 'edit'])->name('riwayat.edit');
        Route::put('/riwayat/{stockMovement}', [SaleController::class, 'update'])->name('riwayat.update');
        
        // Akun
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}/edit', [UserController::class, 'edit']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});
