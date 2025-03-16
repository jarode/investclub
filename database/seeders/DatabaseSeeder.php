<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Podstawowy seeder tworzący konto administratora
        $this->call([
            AdminUserSeeder::class,
        ]);
        
        // Pozostałe seedery będą dodawane w trakcie implementacji POC
    }
}
