<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'user_id' => 1,
                'plan_id' => 1,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 77.00,
                'transaction_id' => 'txn_' . uniqid(),
                'renewal_reminder' => 'enabled',
                'duration_unit' => 'days',
                'duration' => '30',
                'subscription_proof' => null,
            ],
            [
                'user_id' => 1,
                'plan_id' => 2,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'status' => 'active',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 9.99,
                'transaction_id' => 'txn_' . uniqid(),
                'renewal_reminder' => 'enabled',
                'duration_unit' => 'months',
                'duration' => '1',
                'subscription_proof' => null,
            ],
            [
                'user_id' => 1,
                'plan_id' => 3,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->subMonths(1),
                'status' => 'expired',
                'payment_method' => 'bank_transfer',
                'amount_paid' => 99.09,
                'transaction_id' => 'txn_' . uniqid(),
                'renewal_reminder' => 'disabled',
                'duration_unit' => 'weeks',
                'duration' => '1',
                'subscription_proof' => null,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->updateOrInsert(
                ['user_id' => $subscription['user_id'], 'plan_id' => $subscription['plan_id']],
                array_merge($subscription, [
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ])
            );
        }
    }
}
