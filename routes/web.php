<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth')->get('/logout', [AuthController::class, 'logout'])->name('logout');

// katalog publik
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AuthController::class, 'adminUsersIndex'])->name('users.index');
        Route::get('/users/create', [AuthController::class, 'adminUsersCreate'])->name('users.create');
        Route::post('/users', [AuthController::class, 'adminUsersStore'])->name('users.store');
        Route::get('/users/{user}/edit', [AuthController::class, 'adminUsersEdit'])->name('users.edit');
        Route::put('/users/{user}', [AuthController::class, 'adminUsersUpdate'])->name('users.update');
        Route::delete('/users/{user}', [AuthController::class, 'adminUsersDestroy'])->name('users.destroy');

        // Products
        Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories
        Route::get('/categories', [CategoryController::class, 'adminIndex'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Orders (mendukung ?status=pending|processing|completed|cancelled)
        Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

/*
|--------------------------------------------------------------------------
| Customer (login required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Redirect /dashboard lama → dashboard customer
    Route::get('/dashboard', fn () => redirect()->route('customer.dashboard'))->name('dashboard');

    // Halaman customer
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [ProductController::class, 'index'])->name('products'); // dipakai tombol "Lihat Produk"
        // Profile
        Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [CustomerController::class, 'updatePassword'])->name('profile.update.password');
    });

    // Orders (tanpa prefix agar URL singkat)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // pastikan ini setelah /orders/create supaya tidak bentrok
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->whereNumber('order')
        ->name('orders.show');

    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
        ->whereNumber('order')
        ->name('orders.cancel');
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    abort(404, 'Halaman tidak ditemukan.');
});