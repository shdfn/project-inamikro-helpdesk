<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

<<<<<<< HEAD
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
=======
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
>>>>>>> d4779e587f5d906a32ef00c31279318cc940a3ee
        ]);
    }
}
