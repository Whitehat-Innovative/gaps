<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Notification;
use App\Models\NotifyUser;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
      public function getUser(Request $request)
    {
        return auth('sanctum')->user();
    }   

    public function showNotifications(Request $request)
    {
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = $user->notifications;

        return response()->json(['notifications' => $notifications], 200);
    }

    public function showNotification(Request $request, $notification = null)
    {
        $user = $this->getUser($request);

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $check = NotifyUser::where('user_id', $user->id)->where('notification_id', $notification)->exists();
        if (!$check) {
            return response()->json(['error' => 'Notification not found for this user'], 404);
        }   

        $notification_det = Notification::where('id', $notification)->first();

        return response()->json([
            'notification'=>$notification_det
        ], 200);
    }
}
