<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



//Admin routes for products
Route::middleware(['role:admin', 'auth'])->group(function () {

    // Product List route
    Route::get('/admin/products', function () {
        return Inertia::render('Admin/ProductList');  // Product List Vue component
    })->name('admin.product-management');

    // Create new product route
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');

    // Edit existing product route
    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');

    // Order List route
    Route::get('/admin/orders', function () {
        return Inertia::render('Admin/OrderList');  // Product List Vue component
    })->name('admin.order-management');

    // Edit existing order route
    Route::get('/admin/orders/{id}/edit', [OrderController::class, 'edit'])->name('admin.orders.edit');    // Product List route

});

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', function () {
        return Inertia::render('Admin/AdminPanel');
    });
    Route::get('/users', function(){
        return  Inertia::render('Admin/UserList');
    })->name('admin.user-management');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});
//Not Authorized

Route::get('/not-authorized', function(){
        return  Inertia::render('NotAuthorized');
    })->name('not-authorized');

Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');
;

// Route::get('/', function () {
//     return Inertia::render('Home');
// })->name('home');

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
    return Inertia::render('OrderSummary', ['id' => $id]);
})->name('order.show');

// Authentication Routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

//Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

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

// Route for search suggestions (AJAX)
Route::get('/api/search', [SearchController::class, 'search'])->name('search.api');

// Route for showing search results
Route::get('/search/results', [SearchController::class, 'showResults'])->name('search.results');


Route::get('/admin/logs', function () {
    return Inertia::render('Admin/LogList');
})->middleware(['auth', 'role:admin'])->name('admin.logs');

require __DIR__ . '/auth.php';