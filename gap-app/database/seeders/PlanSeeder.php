<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Daily',
                'description' => 'Daily tier with basic access',
                'price' => 77.00,
                'duration' => '24',
                'duration_unit' => 'hours',
                'is_active' => true,
            ],
            [
                'name' => 'Monthly',
                'description' => 'Monthly subscription',
                'price' => 9.99,
                'duration' => '1',
                'duration_unit' => 'months',
                'is_active' => true,
            ],
            [
                'name' => 'Weekly',
                'description' => 'Weekly subscription (best value)',
                'price' => 99.09,
                'duration' => '1',
                'duration_unit' => 'weeks',
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'description' => 'Pro tier with extended features',
                'price' => 99.99,
                'duration' => '1',
                'duration_unit' => 'years',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['name' => $plan['name']],
                array_merge($plan, [
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ])
            );
        }
    }
}
