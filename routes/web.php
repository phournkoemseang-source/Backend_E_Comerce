<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductWebController;
use App\Http\Controllers\Admin\CategoryWebController;

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================
// ADMIN AUTHENTICATION ROUTES (Public)
// ============================================
Route::prefix('admin')->group(function () {
    // Login routes - accessible without authentication
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

// ============================================
// PROTECTED ADMIN ROUTES (Requires Admin Role)
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Products CRUD
    Route::resource('products', ProductWebController::class, [
        'names' => [
            'index' => 'admin.products.index',
            'create' => 'admin.products.create',
            'store' => 'admin.products.store',
            'show' => 'admin.products.show',
            'edit' => 'admin.products.edit',
            'update' => 'admin.products.update',
            'destroy' => 'admin.products.destroy',
        ]
    ]);

    // Categories CRUD
    Route::resource('categories', CategoryWebController::class, [
        'names' => [
            'index' => 'admin.categories.index',
            'create' => 'admin.categories.create',
            'store' => 'admin.categories.store',
            'show' => 'admin.categories.show',
            'edit' => 'admin.categories.edit',
            'update' => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ]
    ]);
});

