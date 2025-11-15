<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Models\Subscription;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes 
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

Route::middleware(['auth:sanctum'])->group(function () {


    Route::prefix('profile')->group(function () {

            Route::post('/update', [MembershipController::class, 'updateProfile']);
    });

    Route::prefix('plan')->group(function () {

            Route::get('/show_plans', [PlanController::class, 'showPlans']);
            Route::get('/show_inactive_plans', [PlanController::class, 'showInactivePlans']);

    });

    Route::prefix('subscriptions')->group(function () {

            Route::post('/share_sub_proof', [SubscriptionController::class, 'shareSubProof']);

            Route::get('/show_active_subs', [SubscriptionController::class, 'showSubs']);
            Route::get('/show_inactive_subs', [SubscriptionController::class, 'showInactiveSubs']);
    });

    Route::prefix('banks')->group(function () {

            Route::post('/share_sub_proof', [SubscriptionController::class, 'shareSubProof']);

            Route::get('/show_active_subs', [SubscriptionController::class, 'showSubs']);
            Route::get('/show_inactive_subs', [SubscriptionController::class, 'showInactiveSubs']);
    });

});

// Email verification route
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
