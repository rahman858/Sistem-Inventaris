<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah admin sudah ada
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]);
        }

        // Cek apakah user biasa sudah ada
        if (!User::where('email', 'user@example.com')->exists()) {
            User::create([
                'name' => 'User Biasa',
                'email' => 'user@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]);
        }
    }
}