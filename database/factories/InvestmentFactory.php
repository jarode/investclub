<?php

namespace Database\Factories;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Investment>
 */
class InvestmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::where('status', 'active')->inRandomOrder()->first();
        
        // Jeśli nie ma aktywnych projektów, stwórz jeden
        if (!$project) {
            $ownerId = User::where('role', 'manager')->inRandomOrder()->first()?->id 
                ?? User::factory()->create(['role' => 'Manager'])->id;
                
            $project = Project::factory()->create([
                'status' => 'active',
                'owner_id' => $ownerId,
            ]);
        }
        
        $minInvestment = $project->min_investment;
        $remainingAmount = $project->remainingAmount();
        
        // Generowanie kwoty inwestycji
        $amount = $this->faker->numberBetween(
            (int)$minInvestment, 
            min((int)($minInvestment * 5), (int)$remainingAmount)
        );
        
        // Jeśli wygenerowana kwota jest większa niż pozostała kwota, używamy połowy pozostałej kwoty
        if ($amount > $remainingAmount) {
            $amount = max($minInvestment, $remainingAmount / 2);
        }
        
        return [
            'user_id' => User::factory(),
            'project_id' => $project->id,
            'amount' => $amount,
            'status' => $this->faker->randomElement(['declared', 'paid', 'confirmed']),
            'transaction_reference' => $this->faker->uuid(),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
    
    /**
     * Wskazuje, że inwestycja została zadeklarowana.
     */
    public function declared(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'declared',
            ];
        });
    }
    
    /**
     * Wskazuje, że inwestycja została opłacona.
     */
    public function paid(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
            ];
        });
    }
    
    /**
     * Wskazuje, że inwestycja została potwierdzona.
     */
    public function confirmed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }
    
    /**
     * Wskazuje, że inwestycja została anulowana.
     */
    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
}
