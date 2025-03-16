<?php

namespace Database\Seeders;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Otrzymaj inwestorów i administratorów
        $investors = User::where('role', 'like', '%investor%')->get();
        $admin = User::where('role', 'like', '%admin%')->first();
        
        if ($investors->isEmpty()) {
            $this->command->info('Brak inwestorów w bazie danych. Tworzę przykładowego inwestora.');
            $investors = [User::factory()->create(['role' => 'Investor', 'verification_status' => 'verified', 'wallet_balance' => 5000])];
        }
        
        // Pobierz aktywne projekty do inwestowania
        $activeProjects = Project::where('status', 'active')->get();
        
        if ($activeProjects->isEmpty()) {
            $this->command->info('Brak aktywnych projektów. Tworzę przykładowy projekt inwestycyjny.');
            $ownerId = User::where('role', 'like', '%manager%')->first()?->id ?? $admin->id;
            $activeProjects = [Project::factory()->create([
                'status' => 'active', 
                'owner_id' => $ownerId,
                'target_amount' => 100000,
                'current_amount' => 0,
                'min_investment' => 1000,
            ])];
        }
        
        // Liczba inwestycji do utworzenia
        $totalInvestments = 30;
        $createdInvestments = 0;
        
        $this->command->info('Tworzenie inwestycji...');
        
        // Dla każdego projektu stwórz losową liczbę inwestycji
        foreach ($activeProjects as $project) {
            // Sprawdzamy, ile inwestycji możemy jeszcze utworzyć
            $remainingInvestments = $totalInvestments - $createdInvestments;
            if ($remainingInvestments <= 0) break;
            
            // Losowa liczba inwestycji dla tego projektu
            $investmentsForProject = min(
                rand(1, 10), 
                $remainingInvestments
            );
            
            for ($i = 0; $i < $investmentsForProject; $i++) {
                // Wybierz losowego inwestora
                $investor = $investors[array_rand($investors->toArray())];
                
                // Sprawdź, czy inwestor ma wystarczające środki
                if ($investor->wallet_balance < $project->min_investment) {
                    // Doładuj portfel inwestora
                    $investor->wallet_balance = $project->min_investment * rand(2, 5);
                    $investor->save();
                }
                
                // Oblicz maksymalną kwotę inwestycji
                $maxInvestment = min(
                    $investor->wallet_balance * 0.8,
                    $project->remainingAmount(),
                    $project->min_investment * 10
                );
                
                if ($maxInvestment < $project->min_investment) {
                    continue; // Nie można zainwestować, przechodzimy do następnej iteracji
                }
                
                // Wygeneruj kwotę inwestycji
                $investmentAmount = rand(
                    (int)$project->min_investment * 100, 
                    (int)$maxInvestment * 100
                ) / 100;
                
                // Losowy status
                $status = ['declared', 'paid', 'confirmed'][rand(0, 2)];
                
                // Utwórz inwestycję
                $investment = Investment::create([
                    'user_id' => $investor->id,
                    'project_id' => $project->id,
                    'amount' => $investmentAmount,
                    'status' => $status,
                    'transaction_reference' => 'SEED-' . uniqid(),
                    'notes' => $this->getRandomNote($status),
                ]);
                
                // Jeśli inwestycja jest potwierdzona, zaktualizuj projekt i portfel inwestora
                if ($status === 'confirmed') {
                    // Odejmij kwotę z portfela inwestora
                    $investor->wallet_balance -= $investmentAmount;
                    $investor->save();
                    
                    // Dodaj kwotę do projektu
                    $project->current_amount += $investmentAmount;
                    $project->save();
                    
                    // Jeśli projekt został w pełni sfinansowany, zmień jego status
                    if ($project->current_amount >= $project->target_amount) {
                        $project->status = 'funded';
                        $project->save();
                        break; // Przejdź do następnego projektu
                    }
                }
                
                $createdInvestments++;
            }
        }
        
        $this->command->info("Utworzono {$createdInvestments} przykładowych inwestycji.");
    }
    
    /**
     * Generuje losową notatkę dla inwestycji na podstawie statusu.
     */
    private function getRandomNote(string $status): ?string
    {
        // Szansa 70% na brak notatki
        if (rand(1, 100) > 30) {
            return null;
        }
        
        $notes = [
            'declared' => [
                'Oczekuję na realizację mojej pierwszej inwestycji!',
                'Projekt wygląda obiecująco, dlatego zdecydowałem się zainwestować.',
                'Deklaracja wstępna, czekam na potwierdzenie.',
            ],
            'paid' => [
                'Płatność zrealizowana przelewem bankowym.',
                'Środki wysłane, czekam na potwierdzenie.',
                'Przelew wykonany, proszę o szybkie potwierdzenie.',
            ],
            'confirmed' => [
                'Inwestycja potwierdzona, dziękuję!',
                'Cieszy mnie udział w tym projekcie.',
                'Liczę na dobry zwrot z inwestycji.',
            ],
        ];
        
        $availableNotes = $notes[$status] ?? [];
        return empty($availableNotes) ? null : $availableNotes[array_rand($availableNotes)];
    }
}
