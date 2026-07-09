<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\LossController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// PWA offline fallback
Route::get('/offline', fn () => view('offline'))->name('offline');

// Language switcher
Route::get('/lang/{locale}', function (Request $request, string $locale) {
    if (in_array($locale, SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back()->withInput();
})->name('lang.switch');

// Landing / Auth
Route::get('/',          [AuthController::class, 'showLanding'])->name('welcome');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login',    [AuthController::class, 'login'])->name('login');
Route::post('/demo',     [AuthController::class, 'demoLogin'])->name('demo.login');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product CSV import (must come before resource to avoid {product} wildcard)
    Route::get('/products/import/upload',   [ProductImportController::class, 'showUpload'])->name('products.import.upload');
    Route::post('/products/import/upload',  [ProductImportController::class, 'upload'])->name('products.import.upload.post');
    Route::get('/products/import/mapping',  [ProductImportController::class, 'showMapping'])->name('products.import.mapping');
    Route::get('/products/import/failures', [ProductImportController::class, 'failures'])->name('products.import.failures');
    Route::post('/products/import/process', [ProductImportController::class, 'process'])->name('products.import.process');

    // Products
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('/api/products/barcode', [ProductController::class, 'searchByBarcode'])->name('products.barcode');

    // Losses
    Route::get('/losses/export', [LossController::class, 'export'])->name('losses.export');
    Route::resource('losses', LossController::class)->except(['edit', 'update']);
});
