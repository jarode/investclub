<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tworzenie konta administratora
        User::factory()->withPersonalTeam()->create([
            'name' => 'Administrator',
            'email' => 'admin@investclub.pl',
            'password' => Hash::make('Test1234!'),
            'role' => 'admin',
            'verification_status' => 'verified',
            'kyc_status' => 'verified',
            'stripe_subscription_status' => 'active',
        ]);
        
        $this->command->info('Pomyślnie utworzono konto administratora:');
        $this->command->info('Email: admin@investclub.pl');
        $this->command->info('Hasło: Test1234!');
    }
}
