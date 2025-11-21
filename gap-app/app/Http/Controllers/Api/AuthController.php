<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password']);

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', PasswordRule::min(8)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);
        // Send email verification notification and require verification before issuing tokens
        $user->sendEmailVerificationNotification();

        $token = $user->createToken('api-token')->plainTextToken;


        return response()->json([
            'message' => 'Registered. A verification link has been sent to your email. Please verify before logging in.',
            'user' => $user,
            'token' => $token,
            
        ], 201);
    }

    /**
     * Verify email using signed URL
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {

            return view('auth.verify-email', ['user' => $user, 'invalid'=>true]);
            // return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            
            return view('auth.verify-email', ['user' => $user, 'verified'=>true]);
            // return response()->json(['message' => 'Email already verified.']);
        }

        if (method_exists($user, 'markEmailAsVerified')) {
            $user->markEmailAsVerified();
        } else {
            $user->email_verified_at = now();
            $user->save();
        }

        event(new Verified($user));

        // Redirect to verification success view
        return view('auth.verify-email', ['user' => $user, 'verify'=>true]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        $validator = Validator::make($credentials, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified. Please verify your email before logging in.'], 403);
        }   

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([ 'user' => $user, 'token' => $token ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            // Revoke current access token
            if ($request->bearerToken()) {
                $user->currentAccessToken()?->delete();
            } else {
                // If session-based, revoke all tokens
                $user->tokens()->delete();
            }
        }

        return response()->json(['message' => 'Logged out']);
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => ['required', 'email']]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // Don't reveal if email exists for security
            return response()->json([
                'message' => 'If an account exists, a confirmation link will be sent.'
            ]);
        }

        // Generate a password reset token
        $token = Password::createToken($user);

        // Create reset URL (customize as needed for your frontend)
        $resetUrl = config('app.frontend_url') . "/password-reset-confirmation/$token?email=" . urlencode($user->email);

        // Send notification with confirmation email
        $user->notify(new PasswordResetConfirmation($resetUrl));
        // $user->password_reset_token = $token;
        // $user->save();

        return response()->json([
            'message' => 'If an account exists with that email, a confirmation link will be sent.',
            'token'=>$token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token'=>'required',
                'email' => 'required|email',
                'password' => ['required', 'confirmed', PasswordRule::min(8)],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if($user && $user->password_reset_token == $request->token) {
            $user->password = Hash::make($request->password);
            $user->password_reset_token = null;
            $user->save();
            return response()->json(['message' => 'Password has been reset successfully.']);

        }else{
            return response()->json(['message' => 'Invalid or expired password reset token.'], 400);    
        }

  
    }

    public function me(Request $request)
    {

        $user = $request->user();  // this NEVER throws

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json($request->user());
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent']);
    }
}
