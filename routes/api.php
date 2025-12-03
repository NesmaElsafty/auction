<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forget-password', [AuthController::class, 'forgetPassword']);
Route::post('verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Public routes for categories and screens (index and show only)
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

Route::get('screens', [ScreenController::class, 'index']);
Route::get('screens/{id}', [ScreenController::class, 'show']);

Route::get('inputs', [InputController::class, 'index']);
Route::get('inputs/{id}', [InputController::class, 'show']);

Route::get('options', [OptionController::class, 'index']);
Route::get('options/{id}', [OptionController::class, 'show']);

Route::get('cities', [CityController::class, 'index']);
Route::get('cities/{id}', [CityController::class, 'show']);

Route::get('regions', [RegionController::class, 'index']);
Route::get('regions/{id}', [RegionController::class, 'show']);

Route::get('agencies', [AgencyController::class, 'index']);
Route::get('agencies/{id}', [AgencyController::class, 'show']);

Route::get('auctions', [AuctionController::class, 'index']);
Route::get('auctions/{id}', [AuctionController::class, 'show']);

// Protected routes
Route::middleware(['auth:sanctum', 'all'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('updateProfilePicture', [AuthController::class, 'updateProfilePicture']);

    Route::get('userAgencies', [AgencyController::class, 'userAgencies']);
    Route::get('userAuctions', [AuctionController::class, 'userAuctions']);
});

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    // prefix agencies
        Route::post('agencies', [AgencyController::class, 'store']);
        Route::put('agencies/{id}', [AgencyController::class, 'update']);
        Route::delete('agencies/{id}', [AgencyController::class, 'destroy']);
        Route::post('agencyAddFiles', [AgencyController::class, 'addFiles']);
        Route::delete('agencyRemoveFiles', [AgencyController::class, 'removeFiles']);

    // Auctions CRUD
        Route::post('auctions', [AuctionController::class, 'store']);
        Route::put('auctions/{id}', [AuctionController::class, 'update']);
        Route::delete('auctions/{id}', [AuctionController::class, 'destroy']);
        Route::post('auctionAddImage', [AuctionController::class, 'addImages']);
        Route::delete('auctionRemoveImage', [AuctionController::class, 'removeImages']);
});
// Admin protected routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('blocklist', [UserController::class, 'blocklist']);
    Route::post('bulkActions', [UserController::class, 'bulkAction']);
    
    // Admin routes for categories (store, update, destroy)
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
    
    // Admin routes for screens (store, update, destroy)
    Route::post('screens', [ScreenController::class, 'store']);
    Route::put('screens/{id}', [ScreenController::class, 'update']);
    Route::delete('screens/{id}', [ScreenController::class, 'destroy']);
    
    // Admin routes for inputs (store, update, destroy)
    Route::post('inputs', [InputController::class, 'store']);
    Route::put('inputs/{id}', [InputController::class, 'update']);
    Route::delete('inputs/{id}', [InputController::class, 'destroy']);
    
    // Admin routes for options (store, update, destroy)
    Route::post('options', [OptionController::class, 'store']);
    Route::put('options/{id}', [OptionController::class, 'update']);
    Route::delete('options/{id}', [OptionController::class, 'destroy']);
    
    // Admin routes for cities (store, update, destroy)
    Route::post('cities', [CityController::class, 'store']);
    Route::put('cities/{id}', [CityController::class, 'update']);
    Route::delete('cities/{id}', [CityController::class, 'destroy']);
    
    // Admin routes for regions (store, update, destroy)
    Route::post('regions', [RegionController::class, 'store']);
    Route::put('regions/{id}', [RegionController::class, 'update']);
    Route::delete('regions/{id}', [RegionController::class, 'destroy']);

    // Admin routes for agencies (bulk actions)
    Route::post('agenciesBulkActions', [AgencyController::class, 'bulkActions']);
});