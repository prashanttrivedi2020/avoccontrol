<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\LossController;
use App\Http\Controllers\Api\ReasonController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Middleware\AdminOnly;
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
Route::get('/login',     [AuthController::class, 'showLanding'])->name('login');
Route::get('/privacy-policy', fn () => view('policy.privacypolicy'))->name('policy.privacypolicy');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
Route::post('/demo',     [AuthController::class, 'demoLogin'])->name('demo.login');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [UserSettingsController::class, 'show'])->name('settings');
    Route::post('/settings', [UserSettingsController::class, 'update'])->name('settings.update');

    Route::middleware(AdminOnly::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\AdminController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/users/{user}/losses', [\App\Http\Controllers\AdminController::class, 'userLosses'])->name('users.losses');
    });

    // Product CSV import (must come before resource to avoid {product} wildcard)
    Route::get('/products/import/upload',   [ProductImportController::class, 'showUpload'])->name('products.import.upload');
    Route::post('/products/import/upload',  [ProductImportController::class, 'upload'])->name('products.import.upload.post');
    Route::get('/products/import/mapping',  [ProductImportController::class, 'showMapping'])->name('products.import.mapping');
    Route::get('/products/import/failures', [ProductImportController::class, 'failures'])->name('products.import.failures');
    Route::post('/products/import/process', [ProductImportController::class, 'process'])->name('products.import.process');

    // Products
    Route::resource('products', ProductController::class)->except(['show']);
    Route::post('/api/products', [ProductController::class, 'quickStore'])->name('products.quick-store');
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/api/products/barcode', [ProductController::class, 'searchByBarcode'])->name('products.barcode');

    // Units
    Route::get('/api/units', [UnitController::class, 'getActive'])->name('api.units.active');
    Route::post('/api/units', [UnitController::class, 'store'])->name('api.units.store');

    // Reasons
    Route::get('/api/reasons', [ReasonController::class, 'getActive'])->name('api.reasons.active');
    Route::post('/api/reasons', [ReasonController::class, 'store'])->name('api.reasons.store');

    Route::resource('units', App\Http\Controllers\UnitManagementController::class)->except(['show']);
    Route::resource('reasons', App\Http\Controllers\ReasonManagementController::class)->except(['show']);

    // Losses
    Route::get('/losses/export', [LossController::class, 'export'])->name('losses.export');
    Route::resource('losses', LossController::class);
});
