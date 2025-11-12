<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Welcome to the admin area',
            'user' => $request->user()?->email,
        ]);
    }
}
