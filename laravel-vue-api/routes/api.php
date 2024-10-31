<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SearchController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Product APIS
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'delete']);
Route::post('/products/{product}/image', [ProductController::class, 'updateImage']);
Route::get('/search', [SearchController::class, 'search']);


// Cart APIs
Route::middleware('web')->group(function () {
    Route::get('/carts/{userId}', [CartController::class, 'show']); // Fetch cart items for a user or session
    Route::post('/carts/{userId}/items', [CartController::class, 'addItem']); // Add item to the cart
    Route::post('/carts/{userId}/items/update', [CartController::class, 'updateItem']); // Update item in the cart
    Route::delete('/carts/{userId}/items', [CartController::class, 'removeItem']); // Remove item from the cart
});


//ORDER routes
Route::post('/orders/{userId}', [OrderController::class, 'create']);
Route::get('/orders/{id}', [OrderController::class, 'show']);

