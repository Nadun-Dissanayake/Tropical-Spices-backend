<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Register
Route::post('/register',[HomeController::class, 'Register']);

//Login
Route::post('/login',[HomeController::class, 'Login']);

//Products
Route::post('/products', [ProductController::class, 'store']);

//Show Products
Route::get('/products', [ProductController::class, 'ShowProducts']);

// Show Single Product
Route::get('/products/{id}', [ProductController::class, 'ShowProductById']);

//Delete Product
Route::delete('/deleteproducts/{id}', [ProductController::class, 'deleteProduct']);

//Update Product
Route::post('/products/{id}', [ProductController::class, 'updateProduct']);
