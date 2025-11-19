<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public routes for categories and screens (index and show only)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/screens', [ScreenController::class, 'index']);
Route::get('/screens/{id}', [ScreenController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});

// Admin protected routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('/blocklist', [UserController::class, 'blocklist']);
    Route::post('/bulkActions', [UserController::class, 'bulkAction']);
    
    // Admin routes for categories (store, update, destroy)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    
    // Admin routes for screens (store, update, destroy)
    Route::post('/screens', [ScreenController::class, 'store']);
    Route::put('/screens/{id}', [ScreenController::class, 'update']);
    Route::delete('/screens/{id}', [ScreenController::class, 'destroy']);
});
