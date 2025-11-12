<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes (API) - register, login, logout, password reset, email verification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes requiring token
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerification']);
    });
});

// Email verification (signed URL) - public GET route
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

// Protected API endpoints for authenticated users with admin role
Route::middleware(['auth:sanctum', 'role_or_permission:admin'])->group(function () {
    Route::get('/admin/stats', function (Request $request) {
        return response()->json([
            'message' => 'Admin statistics endpoint',
            'user' => $request->user(),
            'timestamp' => now(),
        ]);
    });
});
