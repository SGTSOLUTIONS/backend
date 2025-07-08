<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Authenticated, role-based, and guest-only route groups using Sanctum.
*/

// 🚫 Guest-only routes (Unauthenticated users only)
Route::middleware(['guest.only'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// 🔐 Authenticated routes (Sanctum required)
Route::middleware(['auth:sanctum'])->group(function () {

    // ✅ Routes accessible by both admin and user
    Route::middleware('role:admin,user')->group(function () {
        Route::get('/profile-fetch', [ProfileController::class, 'fetch']);
        Route::put('/profile-update', [ProfileController::class, 'update']);
        Route::delete('/profile-delete', [ProfileController::class, 'delete']);

        Route::get('/dashboard', function () {
            return response()->json([
                'message' => 'Welcome User or Admin',
                'user' => auth()->user(),
            ]);
        });
    });

    // 🔒 Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin-only', function () {
            return response()->json(['message' => 'Welcome Admin']);
        });

        // Add any admin-only management routes here
    });

    // 🔓 Logout route for all authenticated users
    Route::post('/logout', [AuthController::class, 'logout']);
});
