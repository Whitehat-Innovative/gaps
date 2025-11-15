<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function shareSubProof(Request $request){

        try {
            $user = $request->user();
        } catch (\Throwable $th) {          
            return response()->json(['error' => 'Unauthorized'], 401);
        } 

        try {
            $validatedData = $request->validate([
                'subscription_proof' => 'required|image|max:2048',
                'renewal_reminder' => 'required|boolean',
                'plan_id' => 'required|integer|exists:plans,id',
                'payment_method' => 'required|string|max:255',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Validation failed', 'details' => $th->getMessage()], 422);
        }

        $subs = new \App\Models\Subscription();
        $subs->user_id = $user->id;

        $path = $request->file('subscription_proof')->store('subscription_proofs', 'public');
        $subs->subscription_proof = \Illuminate\Support\Facades\Storage::url($path);
        $subs->renewal_reminder = $validatedData['renewal_reminder'];
        $subs->plan_id = $validatedData['plan_id'];
        $subs->payment_method = $validatedData['payment_method'];
        $subs->save();

        //email the member and the admin about the new subscription proof
        return response()->json([
            'message' => 'Subscription proof shared successfully',
            'subscription_details'=>$subs
        ], 200);
    }
}
