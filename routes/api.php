<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;

// ============================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('api.admin.login');

Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('api.categories.show');

// Reviews (public read)
Route::get('/reviews', [ReviewController::class, 'index'])->name('api.reviews.index');
Route::get('/reviews/{id}', [ReviewController::class, 'show'])->name('api.reviews.show');

// ============================================
// PROTECTED API ROUTES (Requires Authentication)
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/profile', [AuthController::class, 'profile'])->name('api.profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('api.profile.update');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('api.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('api.orders.show');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('api.checkout.store');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('api.cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('api.cart.store');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('api.cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('api.cart.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('api.wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('api.wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('api.wishlist.destroy');

    // Reviews (authenticated write)
    Route::post('/reviews', [ReviewController::class, 'store'])->name('api.reviews.store');
    Route::put('/reviews/{id}', [ReviewController::class, 'update'])->name('api.reviews.update');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('api.reviews.destroy');
});

// ============================================
// PROTECTED API ROUTES (Requires Authentication + Admin Role)
// ============================================
Route::middleware(['auth:sanctum', 'api.admin'])->group(function () {
    // Admin logout
    Route::post('/admin/logout', [AuthController::class, 'adminLogout'])->name('api.admin.logout');

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
