<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Budi Santoso',
            'username' => 'Budi Santoso',
            'email' => 'mentor@inamikro.com',
            'password' => Hash::make('password'),
            'role' => 'mentor',
        ]);

        User::create([
            'name' => 'Andi Pratama',
            'username' => 'Andi Pratama',
            'email' => 'it@inamikro.com',
            'password' => Hash::make('password'),
            'role' => 'it_support',
        ]);

        User::create([
            'name' => 'Siti Kepala',
            'username' => 'Siti Kepala',
            'email' => 'kepala@inamikro.com',
            'password' => Hash::make('password'),
            'role' => 'kepala_it',
        ]);
    }
}