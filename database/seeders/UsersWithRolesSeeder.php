<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrator
        User::factory()->withPersonalTeam()->create([
            'name' => 'Administrator',
            'email' => 'admin@investclub.pl',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'verification_status' => 'verified',
            'wallet_balance' => 10000.00,
            'kyc_status' => 'verified',
        ]);

        // Manager
        User::factory()->withPersonalTeam()->create([
            'name' => 'Manager',
            'email' => 'manager@investclub.pl',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'verification_status' => 'verified',
            'wallet_balance' => 5000.00,
            'kyc_status' => 'verified',
        ]);

        // Inwestor
        User::factory()->withPersonalTeam()->create([
            'name' => 'Inwestor',
            'email' => 'investor@investclub.pl',
            'password' => Hash::make('password123'),
            'role' => 'investor',
            'verification_status' => 'verified',
            'wallet_balance' => 2500.00,
            'kyc_status' => 'verified',
        ]);

        // Księgowy
        User::factory()->withPersonalTeam()->create([
            'name' => 'Księgowy',
            'email' => 'accountant@investclub.pl',
            'password' => Hash::make('password123'),
            'role' => 'accountant',
            'verification_status' => 'verified',
            'wallet_balance' => 0.00,
            'kyc_status' => 'verified',
        ]);

        // Zwykły użytkownik
        User::factory()->withPersonalTeam()->create([
            'name' => 'Użytkownik',
            'email' => 'user@investclub.pl',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'verification_status' => 'pending',
            'wallet_balance' => 0.00,
            'kyc_status' => 'pending',
        ]);

        // Niezweryfikowany inwestor
        User::factory()->withPersonalTeam()->create([
            'name' => 'Nowy Inwestor',
            'email' => 'new.investor@investclub.pl',
            'password' => Hash::make('password123'),
            'role' => 'investor',
            'verification_status' => 'unverified',
            'wallet_balance' => 0.00,
            'kyc_status' => 'unverified',
        ]);
    }
}
