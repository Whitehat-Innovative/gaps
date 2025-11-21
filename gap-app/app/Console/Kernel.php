<?php

namespace App\Console;

use App\Mail\SubscriptionExpiryNotification;
use App\Models\Subscription;
use Illuminate\Console\Scheduling\Schedule;
use Carbon\Carbon;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            try {

                $now = Carbon::now();

                $subscriptions = Subscription::where('status', 'active') 
                    ->whereDate('end_date', '>=', now()) 
                    ->whereDate('end_date', '<=', now()->addDays(3)) 
                    ->with('user') 
                    ->get();

                foreach ($subscriptions as $subscription) {
                    $user = $subscription->user;
                    $endDate = Carbon::parse($subscription->end_date);
                    $daysLeft = $now->diffInDays($endDate, false);  
                    \Log::info("Scheduler: Subscription ID {$subscription->id} for User ID {$user->id} is expiring in {$daysLeft} days on {$endDate->toDateString()}.");
                    // Here you can add code to send notification emails to users/admins
                
                   Mail::to($user->email)->send(new SubscriptionExpiryNotification($user, $subscription, $daysLeft));
                }

            } catch (\Throwable $e) {
                \Log::error("Scheduler failed: " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

                //         try {
                //     $query = AiUpload::where('created_at', '<', now()->subHour())
                //         ->whereNull('status');

                //     $count = $query->count(); // how many rows match?
                //     \Log::info("Scheduler: Found {$count} uploads older than 1hr with NULL status.");

                //     $updated = $query->update(['status' => 'failed']);
                //     \Log::info("Scheduler: Marked {$updated} uploads as failed.");
                // } catch (\Throwable $e) {
                //     \Log::error("Scheduler failed: " . $e->getMessage(), [
                //         'file' => $e->getFile(),
                //         'line' => $e->getLine(),
                //     ]);
                // }
        })->everyMinute();

        $schedule->call(function () {
        try {
            $now = Carbon::now();

            $subscriptions = \App\Models\Subscription::where('status', 'active')
            ->whereDate('end_date', '>=', now()) 
            ->get();

            foreach ($subscriptions as $subscription) {

                $daysLeft = Carbon::parse($subscription->end_date)->diffInDays($now, false);

                $daysLeft = max($daysLeft, 0);

                $subscription->update([
                    'days_left' => $daysLeft,
                    'status' => $daysLeft == 0 ? 'expired' : $subscription->status,
                ]);
            }

        } catch (\Throwable $e) {
            \Log::error("Scheduler failed: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    })->twiceDaily(0, 12); // every 12 hours


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {

        // \App\Console\Commands\CleanOldUploads::class;

        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

//     protected $commands = [
//     \App\Console\Commands\CleanOldUploads::class,
// ];
}
