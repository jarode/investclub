<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::now()->addDays(fake()->numberBetween(5, 15));
        $endDate = (clone $startDate)->addDays(fake()->numberBetween(30, 180));
        
        $targetAmount = fake()->randomFloat(2, 100000, 5000000); // Od 100 tys. do 5 mln
        $currentAmount = fake()->randomFloat(2, 0, $targetAmount);
        $statusOptions = ['draft', 'active', 'funded', 'completed'];
        
        // Dla testów automatycznie tworzymy właściciela, jeśli nie zostanie explicite podany
        $ownerId = function () {
            return User::factory()->create(['role' => 'Administrator'])->id;
        };
        
        return [
            'name' => fake()->sentence(fake()->numberBetween(3, 8)),
            'description' => fake()->paragraphs(fake()->numberBetween(3, 6), true),
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'min_investment' => fake()->randomElement([1000, 5000, 10000, 25000]),
            'status' => fake()->randomElement($statusOptions),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'returns_projection' => fake()->randomFloat(2, 4, 20), // Od 4% do 20%
            'risk_level' => fake()->randomElement(['low', 'medium', 'high']),
            'owner_id' => $ownerId,
        ];
    }
    
    /**
     * Oznacz projekt jako aktywny.
     */
    public function active(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }
    
    /**
     * Oznacz projekt jako sfinansowany.
     */
    public function funded(): self
    {
        return $this->state(function (array $attributes) {
            $targetAmount = $attributes['target_amount'] ?? 1000000;
            
            return [
                'status' => 'funded',
                'current_amount' => $targetAmount,
            ];
        });
    }
    
    /**
     * Oznacz projekt jako ukończony.
     */
    public function completed(): self
    {
        return $this->state(function (array $attributes) {
            $targetAmount = $attributes['target_amount'] ?? 1000000;
            $endDate = Carbon::now()->subDays(fake()->numberBetween(1, 30));
            $startDate = (clone $endDate)->subDays(fake()->numberBetween(60, 180));
            
            return [
                'status' => 'completed',
                'current_amount' => $targetAmount,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }
    
    /**
     * Oznacz projekt jako w fazie szkicu.
     */
    public function draft(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'current_amount' => 0,
            ];
        });
    }
    
    /**
     * Ustaw określony poziom ryzyka dla projektu.
     */
    public function riskLevel(string $level): self
    {
        if (!in_array($level, ['low', 'medium', 'high'])) {
            $level = 'medium';
        }
        
        return $this->state(function (array $attributes) use ($level) {
            return [
                'risk_level' => $level,
            ];
        });
    }
}
