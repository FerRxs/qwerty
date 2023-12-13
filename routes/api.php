<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

// Rutas de Autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rutas CRUD para Productos, Categorías, Usuarios y Órdenes
Route::middleware(['auth:sanctum'])->group(function () {
    // Productos
    Route::apiResource('/products', ProductController::class);

    // Categorías
    Route::apiResource('/categories', CategoryController::class);

    // Usuarios
    Route::apiResource('/users', UserController::class);

    // Órdenes
    Route::apiResource('/orders', OrderController::class);
});
