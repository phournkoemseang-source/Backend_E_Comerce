<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;

<<<<<<< HEAD
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
=======
// ============================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('api.admin.login');


Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');


Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show');


// User profile route
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ============================================
// PROTECTED API ROUTES (Requires Authentication + Admin Role)
// ============================================
Route::middleware(['auth:sanctum', 'api.admin'])->group(function () {
    // Products CRUD (Admin only)
    Route::post('/products', [ProductController::class, 'store'])->name('api.products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('api.products.update');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('api.products.patch');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('api.products.destroy');

    
    // Categories CRUD (Admin only)
    Route::post('/categories', [CategoryController::class, 'store'])->name('api.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('api.categories.patch');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('api.categories.destroy');
});
>>>>>>> 88b724b9bdd90fa09171c644ceb9ef6acb01ba02
