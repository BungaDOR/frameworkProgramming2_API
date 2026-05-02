<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\BukuApiController;
use App\Http\Controllers\Api\ProdukApiController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;

Route::apiResource('users', UserApiController::class);
Route::apiResource('bukus', BukuApiController::class);
Route::apiResource('produks', ProdukApiController::class);
Route::post('/produks/{id}/images', [ProdukApiController::class, 'uploadImages']);
Route::put('/produks/{id}/images', [ProdukApiController::class, 'updateImages']);
Route::delete('/produks/{id}/images/{imageId}', [ProdukApiController::class, 'deleteImages']);
Route::apiResource('orders', OrderController::class);

Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user(); // Contoh return data user yg login
    });
    Route::post('/logout', [AuthController::class, 'logout']);
});
