<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Product APIS
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('logs');
Route::delete('/products/{id}', [ProductController::class, 'delete'])->middleware('logs');
Route::post('/products/{product}/image', [ProductController::class, 'updateImage'])->middleware('logs');
Route::get('/search', [SearchController::class, 'search']);
Route::get('/products/export', [ExportImportController::class, 'exportProducts'])->name('products.export');
Route::post('/products/import', [ExportImportController::class, 'importProducts'])->name('products.import');

// Cart APIs
Route::middleware('web')->group(function () {
    Route::get('/carts/{userId}', [CartController::class, 'show']); 
    Route::post('/carts/{userId}/items', [CartController::class, 'addItem'])->middleware('logs');
    Route::post('/carts/{userId}/items/update', [CartController::class, 'updateItem'])->middleware('logs');
    Route::delete('/carts/{userId}/items', [CartController::class, 'removeItem'])->middleware('logs');
});


//ORDER routes
Route::post('/orders/{userId}', [OrderController::class, 'create'])->middleware('logs');
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');

//User routes
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store'])->middleware('logs');
Route::put('/user/{id}', [UserController::class, 'update'])->middleware('logs');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('logs');
Route::get('/users/export', [ExportImportController::class, 'exportUsers'])->name('users.export');
Route::post('/users/import', [ExportImportController::class, 'importUsers'])->name('users.import');
Route::get('/logs', [LogController::class, 'index']);