<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class BankController extends Controller
{
   
     public function getUser(Request $request)
    {
        return auth('sanctum')->user();
    }

    public function showBanks(Request $request){

        
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $banks = Bank::all();

        return response()->json([
            'banks'=>$banks
        ], 200);
    }  
    
    public function showBank(Request $request, $bank= null){

       $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bank_det = Bank::findOrFail($bank);

        return response()->json([
            'bank'=>$bank_det
        ], 200);
    }  
}
