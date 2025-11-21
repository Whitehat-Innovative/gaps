<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Models\Subscription;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

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


            Route::get('/show_active_subs', [SubscriptionController::class, 'showAllSubs']);

            Route::get('/show_single_sub/{subs_id?}', [SubscriptionController::class, 'showSingleSub']);

            Route::get('/show_inactive_subs', [SubscriptionController::class, 'showInactiveSubs']);
    });

});

Route::prefix('payment')->group(function () {

            Route::post('/initialize_payment', [SubscriptionController::class, 'initiatePayment']);

            Route::post('/renew_subscription/{subs_id?}', [SubscriptionController::class, 'renewPayment']);

            Route::post('/share_sub_proof/{subs_id?}', [SubscriptionController::class, 'shareSubProof']);

            Route::get('/check_status/{subs_id?}', [SubscriptionController::class, 'showSingleSub']);
    
    });

Route::prefix('bank')->group(function () {

            Route::get('/show_banks', [BankController::class, 'showBanks']);

            Route::get('/show_bank/{bank?}', [BankController::class, 'showBank']);

    });

Route::prefix('notification')->group(function () {

            Route::get('/show_notifications', [NotificationController::class, 'showNotifications']);

            Route::get('/show_notification/{notification?}', [NotificationController::class, 'showNotification']);
    });


Route::get('/send-test-notification', function () {


//     $factory = (new \Kreait\Firebase\Factory)
//     ->withServiceAccount(storage_path('firebase/fitbase-3a4eb-firebase-adminsdk-fbsvc-1640bca54b.json'));
// $messaging = $factory->createMessaging();

//     $path = storage_path('app/firebase/fitbase-3a4eb-firebase-adminsdk-fbsvc-1640bca54b.json');
// dd($path, file_exists($path));


    // dd(file_exists(storage_path('firebase/fitbase-3a4eb-firebase-adminsdk-fbsvc-1640bca54b.json')));


    // dd(storage_path('firebase/fitbase-3a4eb-firebase-adminsdk-fbsvc-1640bca54b.json'));


    // dd('var/www/html/gap/gap-app/storage/firebase/fitbase-3a4eb-firebase-adminsdk-fbsvc-1640bca54b.json');

    // Initialize Firebase
    $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase-messaging-sw.js'));
    $messaging = $factory->createMessaging();

    // Your FCM token (from browser)
    $token = "c6Yn1BG_YAtyuHV77cqsNC:APA91bGH_KGq8P-s4ZeXqLJD4R9Qf_6z40kaB6reFpJM96JQCh3TmOT566CBTHS7_M7fzXYuoxS5y56nQaB8s-4jxmYSSy9BwfYSpg6IWFyUjmCP0kvGYcU";

    // Create notification
    $notification = Notification::create('Test Notification', 'Hello from Laravel');

    // Create message for target token
    $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);

    // Send the message
    $messaging->send($message);

    return "Notification sent!";
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
