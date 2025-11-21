<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            [
                'title' => 'Welcome to Our Service',
                'message' => 'Hello! Thank you for joining our platform.',
                'is_sent' => true,
                'type' => 'welcome',
                'total_sent_to' => 10,
                'icon' => null,
            ],
            [
                'title' => 'Subscription Expiring Soon',
                'message' => 'Your subscription will expire in 3 days. Renew to continue enjoying our services.',
                'is_sent' => true,
                'type' => 'subscription',
                'total_sent_to' => 10,
                'icon' => null,
            ],
            [
                'title' => 'New Feature Released',
                'message' => 'We have added a new feature to improve your experience.',
                'is_sent' => true,
                'type' => 'feature',
                'total_sent_to' => 10,
                'icon' => null,
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->updateOrInsert(
                ['title' => $notification['title']], // use unique field(s) to avoid duplicates
                array_merge($notification, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}
