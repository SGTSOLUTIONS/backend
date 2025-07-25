<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\ForgotPasswordController;
use App\Http\Controllers\Api\v1\LayerController;

/*
|--------------------------------------------------------------------------
| api Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['cors'])->group(function () {

// 🚫 Guest-only routes (Unauthenticated only)
Route::middleware(['guest.only'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Password reset (email/OTP/token-based)
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/submit-otp', [ForgotPasswordController::class, 'verifyOtp']); // Optional
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

// 🔐 Authenticated routes (Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {

    // ✅ Routes accessible by both admin and user
    Route::middleware('role:admin,user')->group(function () {
        Route::get('/dashboard', fn () => response()->json([
            'message' => 'Welcome User or Admin',
            'user' => auth()->user(),
        ]));
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    });

    // 🔒 Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin-only', fn () => response()->json([
            'message' => 'Welcome Admin'
        ]));
        Route::post('/createlayers', [LayerController::class ,'store']);
        Route::get('/fetch-layers', [LayerController::class ,'fetch']);
    });

    // 🔓 Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});
});