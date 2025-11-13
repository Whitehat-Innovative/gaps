<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
Use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
     public function showPlans(Request $request)
    {
        try {
            $user = $request->user();
        } catch (\Throwable $th) {          
            return response()->json(['error' => 'Unauthorized'], 401);
        }   

        $plans = Plan::where('is_active', true)->get();
        return response()->json(['plans' => $plans], 200);
    }

     public function showInactivePlans(Request $request)
    {
        try {
            $user = $request->user();
        } catch (\Throwable $th) {          
            return response()->json(['error' => 'Unauthorized'], 401);
        }   

        $plans = Plan::where('is_active', false)->get();
        return response()->json(['plans' => $plans], 200);
    }
}

