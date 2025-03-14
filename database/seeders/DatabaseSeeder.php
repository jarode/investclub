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
        // Wykomentuj domyślne tworzenie użytkownika testowego, ponieważ teraz używamy seedera ról
        // User::factory()->withPersonalTeam()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        // ]);

        // Uruchom seeder dla użytkowników z rolami
        $this->call([
            UsersWithRolesSeeder::class,
            ProjectsSeeder::class,
        ]);
    }
}
