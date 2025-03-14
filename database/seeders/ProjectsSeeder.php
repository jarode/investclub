<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sprawdź, czy istnieją użytkownicy typu admin/manager
        $adminsCount = User::where('role', 'admin')->count();
        $managersCount = User::where('role', 'manager')->count();
        
        if ($adminsCount === 0 && $managersCount === 0) {
            $this->command->error('Brak użytkowników typu admin/manager. Najpierw uruchom UsersWithRolesSeeder.');
            return;
        }
        
        // Utwórz projekty w różnych statusach
        // 1. Projekty w fazie szkicu
        Project::factory()->count(3)->draft()->create();
        
        // 2. Projekty aktywne
        // 2.1 Projekty o niskim ryzyku
        Project::factory()->count(2)->active()->riskLevel('low')->create();
        
        // 2.2 Projekty o średnim ryzyku
        Project::factory()->count(3)->active()->riskLevel('medium')->create();
        
        // 2.3 Projekty o wysokim ryzyku
        Project::factory()->count(2)->active()->riskLevel('high')->create();
        
        // 3. Projekty sfinansowane
        Project::factory()->count(3)->funded()->create();
        
        // 4. Projekty zakończone
        Project::factory()->count(2)->completed()->create();
        
        $this->command->info('Utworzono 15 przykładowych projektów inwestycyjnych.');
    }
}
