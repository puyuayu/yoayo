<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController; // TAMBAHKAN INI
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth')->get('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes (bisa diakses semua orang)
Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');

// Admin Routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Users Management Routes
    Route::get('/users', [AuthController::class, 'adminUsersIndex'])->name('users.index');
    Route::get('/users/create', [AuthController::class, 'adminUsersCreate'])->name('users.create');
    Route::post('/users', [AuthController::class, 'adminUsersStore'])->name('users.store');
    Route::get('/users/{user}/edit', [AuthController::class, 'adminUsersEdit'])->name('users.edit');
    Route::put('/users/{user}', [AuthController::class, 'adminUsersUpdate'])->name('users.update');
    Route::delete('/users/{user}', [AuthController::class, 'adminUsersDestroy'])->name('users.destroy');
    
    // Products Admin Routes
    Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    // Categories Admin Routes
    Route::get('/categories', [CategoryController::class, 'adminIndex'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Orders Admin Routes
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

// Customer Routes - YANG DIPERBAIKI
Route::middleware('auth')->group(function () {
    // Dashboard customer dengan controller
    Route::get('/customer/products', [ProductController::class, 'index'])
    ->name('customer.products');
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    
    // Profile customer
    Route::get('/customer/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::put('/customer/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    Route::put('/customer/profile/password', [CustomerController::class, 'updatePassword'])
    ->name('customer.profile.update.password');
    
    // Route dashboard lama (redirect ke customer dashboard)
    Route::get('/dashboard', function () {
        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    // Orders customer
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

Route::fallback(function () {
    abort(404, 'Halaman tidak ditemukan.');
});