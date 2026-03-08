<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;

Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items',OrderItemController::class);
    Route::post("/products",[ProductController::class,'store']);
    Route::put("/products/{id}",[ProductController::class,'update']);
    Route::delete("/products/{id}",[ProductController::class,'destroy']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

});