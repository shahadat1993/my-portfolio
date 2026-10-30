<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Change these in .env (ADMIN_EMAIL / ADMIN_PASSWORD) BEFORE seeding on a live server.
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            ['name' => env('ADMIN_NAME', 'Admin'), 'password' => env('ADMIN_PASSWORD', 'password123'), 'is_admin' => true]
        );
    }
}
