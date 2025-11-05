<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/books', [BookController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// المسارات الخاصة بالمستخدم المسجل
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/books', [BookController::class, 'myBooks']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);


// المسارات الخاصه بسيستم الطلبات
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);              // إنشاء طلب
    Route::get('/orders/mine', [OrderController::class, 'myOrders']);       // طلباتي كمشتري
    Route::get('/orders/received', [OrderController::class, 'receivedOrders']); // الطلبات اللي جاتلي كبائع
    Route::post('/orders/{id}', [OrderController::class, 'updateStatus']);   // قبول / رفض الطلب
});
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{bookId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{bookId}', [FavoriteController::class, 'destroy']);
});
