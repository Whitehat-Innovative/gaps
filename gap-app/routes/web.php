<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';


route::get('/index.html', function () {
    return view('index');
})->name('in');    


Route::get('/send-notification', function (\Illuminate\Http\Request $request) {
    

    $contents = 'hello';
    $subscription_id = '0bb97ead-9b80-4538-969d-a0a6217af7b5';
    $url = 'https://goggle.com';  


    $restKey = env('ONESIGNAL_REST_API_KEY');
    $auth = base64_encode($restKey . ":");
    
    try {
    $restKey = env('ONESIGNAL_REST_API_KEY');
    $auth = base64_encode($restKey . ":");

    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . $auth,
        'Content-Type' => 'application/json',
    ])->post('https://api.onesignal.com/notifications', [
        'app_id' => '8b4589ed-8346-4283-9e28-6f0c2a32203e',
        'include_player_ids' => $subscription_id,
        'contents' => ['en' => $contents],
        'url' => $url,
    ]);

    return $response->body();

} catch (\Exception $e) {
    return response()->json([
        'error' => 'Failed to send notification',
        'message' => $e->getMessage()
    ], 500);
} 
});