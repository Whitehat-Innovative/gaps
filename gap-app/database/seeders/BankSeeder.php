<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // basic bank records - updateOrInsert will skip if name exists
        $banks = [
            [
                'bank_name' => 'First National Bank',
                'account_number' => '1234567890',
                'account_holder_name' => 'Acme Corp',
                'status' => 'active',
                'bank_logo' => null,
            ],
            [
                'bank_name' => 'Global Savings',
                'account_number' => '0987654321',
                'account_holder_name' => 'Acme Corp',
                'status' => 'active',
                'bank_logo' => null,
            ],
            [
                'bank_name' => 'Commerce Trust',
                'account_number' => '1122334455',
                'account_holder_name' => 'Acme Corp',
                'status' => 'active',
                'bank_logo' => null,
            ],
        ];

        foreach ($banks as $bank) {
            DB::table('banks')->updateOrInsert(
                ['bank_name' => $bank['bank_name']],
                array_merge($bank, [
                    'updated_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ])
            );
        }
    }
}
