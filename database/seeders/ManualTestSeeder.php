<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManualTestSeeder extends Seeder
{
    /**
     * Seed the database with manual test data.
     */
    public function run(): void
    {
        // Czyszczenie istniejących danych
        $this->command->info('Czyszczenie istniejących danych...');
        \App\Models\Investment::query()->delete();
        Project::query()->delete();
        User::query()->where('role', '!=', 'super_admin')->delete();
        
        // 1. Tworzenie użytkowników z rolami
        $this->command->info('Tworzenie użytkowników testowych...');
        
        // Administrator
        $admin = User::factory()->withPersonalTeam()->create([
            'name' => 'Administrator',
            'email' => 'admin@investclub.pl',
            'password' => Hash::make('Test1234!'),
            'role' => 'admin',
            'kyc_status' => 'verified',
        ]);
        
        // Manager
        $manager = User::factory()->withPersonalTeam()->create([
            'name' => 'Manager',
            'email' => 'manager@investclub.pl',
            'password' => Hash::make('Test1234!'),
            'role' => 'manager',
            'kyc_status' => 'verified',
        ]);
        
        // Inwestor 1
        $investor1 = User::factory()->withPersonalTeam()->create([
            'name' => 'Inwestor',
            'email' => 'inwestor@investclub.pl',
            'password' => Hash::make('Test1234!'),
            'role' => 'investor',
            'kyc_status' => 'verified',
        ]);
        
        // Inwestor 2
        $investor2 = User::factory()->withPersonalTeam()->create([
            'name' => 'Inwestor 2',
            'email' => 'inwestor2@investclub.pl',
            'password' => Hash::make('Test1234!'),
            'role' => 'investor',
            'kyc_status' => 'pending',
        ]);
        
        // 2. Tworzenie projektów
        $this->command->info('Tworzenie projektów testowych...');
        
        // Projekt 1: Budowa biurowca Alpha
        $project1 = Project::create([
            'name' => 'Budowa biurowca Alpha',
            'description' => 'Inwestycja w budowę nowoczesnego biurowca klasy A w centrum biznesowym. Projekt obejmuje 10 pięter powierzchni biurowej z parkingiem podziemnym.',
            'target_amount' => 10000000.00,
            'min_investment' => 1000000.00,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'returns_projection' => 8.5,
            'risk_level' => 'medium',
            'owner_id' => $manager->id,
            'category' => 'nieruchomości komercyjne',
            'location' => 'Warszawa',
        ]);
        
        // Projekt 2: Apartamenty Omega
        $project2 = Project::create([
            'name' => 'Apartamenty Omega',
            'description' => 'Budowa kompleksu luksusowych apartamentów w nadmorskiej lokalizacji. Projekt obejmuje 50 apartamentów z widokiem na morze oraz infrastrukturę rekreacyjną.',
            'target_amount' => 20000000.00,
            'min_investment' => 2000000.00,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonths(8),
            'returns_projection' => 12.0,
            'risk_level' => 'medium',
            'owner_id' => $manager->id,
            'category' => 'nieruchomości mieszkaniowe',
            'location' => 'Gdynia',
        ]);
        
        // Projekt 3: TechStartup Beta
        $project3 = Project::create([
            'name' => 'TechStartup Beta',
            'description' => 'Inwestycja w innowacyjny startup technologiczny rozwijający aplikację do zarządzania inwestycjami z wykorzystaniem sztucznej inteligencji.',
            'target_amount' => 5000000.00,
            'min_investment' => 500000.00,
            'status' => 'draft',
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'returns_projection' => 15.0,
            'risk_level' => 'high',
            'owner_id' => $manager->id,
            'category' => 'startupy',
            'location' => 'Poznań',
        ]);
        
        $this->command->info('Dane testowe zostały pomyślnie utworzone!');
        $this->command->info('-------------------------------------------');
        $this->command->info('Użytkownicy:');
        $this->command->info('- Admin: admin@investclub.pl / Test1234!');
        $this->command->info('- Manager: manager@investclub.pl / Test1234!');
        $this->command->info('- Inwestor: inwestor@investclub.pl / Test1234! (KYC: zweryfikowany)');
        $this->command->info('- Inwestor2: inwestor2@investclub.pl / Test1234! (KYC: w trakcie)');
        $this->command->info('-------------------------------------------');
        $this->command->info('Projekty:');
        $this->command->info('- Budowa biurowca Alpha: 10 000 000 zł, min. 1 000 000 zł');
        $this->command->info('- Apartamenty Omega: 20 000 000 zł, min. 2 000 000 zł');
        $this->command->info('- TechStartup Beta: 5 000 000 zł, min. 500 000 zł (wstrzymany)');
    }
} 