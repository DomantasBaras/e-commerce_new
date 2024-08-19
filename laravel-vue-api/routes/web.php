<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/products', function () {
    // Your product page logic
})->name('products');

Route::get('/products/{id}', function ($id) {
    return Inertia::render('ProductDetail', ['id' => $id]);
})->name('products.detail');

Route::get('/cart', function () {
    return Inertia::render('Cart');
})->name('cart');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/order-summary', function () {
    // Your product page logic
})->name('order');



Route::get('/order-summary/{id}', function ($id) {
    return Inertia::render('OrderSummary', ['id' => $id ]);
})->name('order.show');

// Authentication Routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Registration Route
Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.post');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
