<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tworzenie konta administratora
        $admin = new User();
        $admin->name = 'Administrator';
        $admin->email = 'admin@investclub.pl';
        $admin->email_verified_at = now();
        $admin->password = Hash::make('Test1234!');
        $admin->remember_token = Str::random(10);
        $admin->role = 'Administrator';
        $admin->kyc_status = 'verified';
        $admin->save();
        
        $this->command->info('Pomyślnie utworzono konto administratora:');
        $this->command->info('Email: admin@investclub.pl');
        $this->command->info('Hasło: Test1234!');
    }
}
