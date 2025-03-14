<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test czy relacja do właściciela projektu działa poprawnie
     */
    public function test_owner_relation(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create(['owner_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $project->owner);
        $this->assertEquals($user->id, $project->owner->id);
    }
    
    /**
     * Test czy metoda isActive działa poprawnie
     */
    public function test_is_active_method(): void
    {
        $project = Project::factory()->create(['status' => 'active']);
        $this->assertTrue($project->isActive());
        
        $project = Project::factory()->create(['status' => 'draft']);
        $this->assertFalse($project->isActive());
    }
    
    /**
     * Test czy metoda isFunded działa poprawnie
     */
    public function test_is_funded_method(): void
    {
        // Poprzez status
        $project = Project::factory()->create(['status' => 'funded']);
        $this->assertTrue($project->isFunded());
        
        // Poprzez osiągnięcie kwoty
        $project = Project::factory()->create([
            'status' => 'active',
            'target_amount' => 1000,
            'current_amount' => 1000
        ]);
        $this->assertTrue($project->isFunded());
        
        // Gdy warunki nie są spełnione
        $project = Project::factory()->create([
            'status' => 'active',
            'target_amount' => 1000,
            'current_amount' => 500
        ]);
        $this->assertFalse($project->isFunded());
    }
    
    /**
     * Test czy metoda isCompleted działa poprawnie
     */
    public function test_is_completed_method(): void
    {
        $project = Project::factory()->create(['status' => 'completed']);
        $this->assertTrue($project->isCompleted());
        
        $project = Project::factory()->create(['status' => 'active']);
        $this->assertFalse($project->isCompleted());
    }
    
    /**
     * Test czy metoda fundingPercentage działa poprawnie
     */
    public function test_funding_percentage_method(): void
    {
        $project = Project::factory()->create([
            'target_amount' => 10000,
            'current_amount' => 2500
        ]);
        $this->assertEquals(25, $project->fundingPercentage());
        
        // Gdy wartość docelowa jest zero
        $project = Project::factory()->create([
            'target_amount' => 0,
            'current_amount' => 100
        ]);
        $this->assertEquals(0, $project->fundingPercentage());
        
        // Gdy funding przekracza 100%
        $project = Project::factory()->create([
            'target_amount' => 1000,
            'current_amount' => 1500
        ]);
        $this->assertEquals(150, $project->fundingPercentage());
    }
    
    /**
     * Test czy metoda remainingAmount działa poprawnie
     */
    public function test_remaining_amount_method(): void
    {
        $project = Project::factory()->create([
            'target_amount' => 10000,
            'current_amount' => 4000
        ]);
        $this->assertEquals(6000, $project->remainingAmount());
        
        // Gdy projekt jest już w pełni sfinansowany
        $project = Project::factory()->create([
            'target_amount' => 10000,
            'current_amount' => 12000
        ]);
        $this->assertEquals(0, $project->remainingAmount());
    }
    
    /**
     * Test czy metoda isOwnedBy działa poprawnie
     */
    public function test_is_owned_by_method(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        
        $project = Project::factory()->create(['owner_id' => $user->id]);
        
        $this->assertTrue($project->isOwnedBy($user));
        $this->assertFalse($project->isOwnedBy($anotherUser));
    }
    
    /**
     * Test czy caster deklamacji dla dat działa poprawnie
     */
    public function test_date_casting(): void
    {
        $project = Project::factory()->create([
            'start_date' => '2023-01-01',
            'end_date' => '2023-12-31'
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $project->start_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $project->end_date);
        $this->assertEquals('2023-01-01', $project->start_date->toDateString());
        $this->assertEquals('2023-12-31', $project->end_date->toDateString());
    }
    
    /**
     * Test czy caster deklaracji dla liczb działa poprawnie
     */
    public function test_decimal_casting(): void
    {
        $project = Project::factory()->create([
            'target_amount' => '5000.50',
            'current_amount' => '1000.25',
            'min_investment' => '100.75',
            'returns_projection' => '15.50'
        ]);
        
        $this->assertIsString($project->getAttributes()['target_amount']);
        $this->assertIsString($project->getAttributes()['current_amount']);
        $this->assertIsString($project->getAttributes()['min_investment']);
        $this->assertIsString($project->getAttributes()['returns_projection']);
        
        $this->assertEquals(5000.50, $project->target_amount);
        $this->assertEquals(1000.25, $project->current_amount);
        $this->assertEquals(100.75, $project->min_investment);
        $this->assertEquals(15.50, $project->returns_projection);
    }
}
