<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

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
