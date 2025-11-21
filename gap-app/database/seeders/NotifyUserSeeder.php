<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotifyUserSeeder extends Seeder
{
   public function run(): void
    {

        $notifyUsers = [
            [
                'user_id' => 1,
                'notification_id' => 1,
            ],
            [
                'user_id' => 1,
                'notification_id' => 2,
            ],
            [
                'user_id' => 2,
                'notification_id' => 1,
            ],
            [
                'user_id' => 3,
                'notification_id' => 3,
            ],
        ];

        foreach ($notifyUsers as $notifyUser) {
            DB::table('notify_users')->updateOrInsert(
                [
                    'user_id' => $notifyUser['user_id'],
                    'notification_id' => $notifyUser['notification_id'],
                ],
                array_merge($notifyUser, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}
