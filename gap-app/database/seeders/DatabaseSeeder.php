<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $name = Str::random(5);
        User::factory()->create([
            'name' => 'Test User',
            'email' => $name.'@example.com',
        ]);

        // Seed roles and permissions
        $this->call(\Database\Seeders\RolePermissionSeeder::class);
        // Seed plans
        $this->call(\Database\Seeders\PlanSeeder::class);
    }
}
