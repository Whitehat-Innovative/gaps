<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->middleware('role_or_permission:admin');
