<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{

     public function getUser(Request $request)
    {
        return auth('sanctum')->user();
    }  

    public function renewPayment(Request $request, $subs_id = null){

        
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $old_sub = Subscription::find($subs_id);

        if(!$old_sub){
            return response()->json(['error' => 'Subscription not found'], 404);
        }   

        $new_sub = $old_sub->replicate();
        $new_sub->status = 'pending';
        $new_sub->start_date = null;
        $new_sub->end_date = null;
        $new_sub->amount_paid = null;
        $new_sub->transaction_id = null;
        $new_sub->duration_unit = null;
        $new_sub->duration = null;
        $new_sub->subscription_proof = null;
  
        $new_sub->save();

        //email the member and the admin about the new subscription proof

        return response()->json([
            'message' => 'Subscription Renewed Successfully, please proceed to share payment proof again.',
            'subscription_details'=>$new_sub
        ], 200);
    }

    public function initiatePayment(Request $request){

        
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $validatedData = $request->validate([
            'renewal_reminder' => 'required|boolean',
            'plan_id' => 'required|integer|exists:plans,id',
            'payment_method' => 'required|string|max:255',
        ]);

        if (!$validatedData) {
            return response()->json(['error' => 'Validation failed'], 422);
        }
      

        $subs = new Subscription();
        $subs->user_id = $user->id;
        $subs->renewal_reminder = $validatedData['renewal_reminder'];
        $subs->plan_id = $validatedData['plan_id'];
        $subs->payment_method = $validatedData['payment_method'];
        $subs->save();

        //email the member and the admin about the new subscription proof

        return response()->json([
            'message' => 'Subscription Initiated Successfully',
            'subscription_details'=>$subs
        ], 200);
    }

    public function shareSubProof(Request $request, $subs_id = null){

        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $validator = Validator::make($request->all(), [
            'subscription_proof' => 'required|image|max:2048',
        ]);

        if (!$validator->passes()) {
            return response()->json(['errors' => $validator->errors()], 422);   
        }

        $subs = Subscription::findOrFail($subs_id);
        
        $path = $request->file('subscription_proof')->store('subscription_proofs', 'public');
        $subs->subscription_proof = \Illuminate\Support\Facades\Storage::url($path);
        $subs->save();

        //email the member and the admin about the new subscription proof
        return response()->json([
            'message' => 'Subscription proof shared successfully, Please wait for approval from admin a mial will be sent and you can check if the status of payment has been updated.',
            'subscription_details'=>$subs
        ], 200);
    }

    public function showInactiveSubs(Request $request){

        try {
            $user = $request->user();
        } catch (\Throwable $th) {          
            return response()->json(['error' => 'Unauthorized'], 401);
        } 

        $subs = Subscription::where('user_id', $user->id)
                ->where('status', 'expired')
                ->get();

        return response()->json([
            'inactive_subscriptions'=>$subs
        ], 200);
    }   

    public function showAllSubs(Request $request){

        try {
            $user = $request->user();
        } catch (\Throwable $th) {          
            return response()->json(['error' => 'Unauthorized'], 401);
        } 

        $subs = Subscription::where('user_id', $user->id)
                ->get();

        return response()->json([
            'subscriptions'=>$subs
        ], 200);
    }

    public function showSingleSub(Request $request, $subs_id = null){

        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $sub = Subscription::where('user_id', $user->id)
                ->where('id', $subs_id)   
                ->get();

        return response()->json([
            'subscription'=>$sub
        ], 200);
    }

    

   
}
