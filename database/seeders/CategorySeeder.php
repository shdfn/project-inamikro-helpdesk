<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Login/Akses']);
        Category::create(['name' => 'Performa']);
        Category::create(['name' => 'Data']);
        Category::create(['name' => 'File/Dokumen']);
    }
}