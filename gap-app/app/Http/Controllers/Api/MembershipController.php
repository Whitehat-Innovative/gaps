<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembershipController extends Controller
{
      public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
        } catch (\Throwable $th) {

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
                    $validatedData = $request->validate([
                    'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'age' => 'nullable|string|max:3',
                    'phone' => 'nullable|string|max:15',
                    'weight' => 'nullable|string|max:10',
                    'location' => 'nullable|string|max:255',
                    'bio' => 'nullable|string|max:1000', 
                    'gender' => 'nullable|string|max:50',     
                ]);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Validation failed', 'details' => $th->getMessage()], 422);
        }


        if ($request->hasFile('profile_picture')) {

            if ($user->profile_picture) {
                $oldPath = str_replace('/storage/', '', $user->profile_picture);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validatedData['profile_picture'] = Storage::url($path);
        }


        $user->fill($validatedData);
        if (isset($path)) {
            $user->profile_picture = Storage::url($path);
        }
        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }

}
