<?php

namespace Tests\Unit;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentTest extends TestCase
{
    use RefreshDatabase;

    private User $investor;
    private Project $project;
    private Investment $investment;

    protected function setUp(): void
    {
        parent::setUp();

        // Przygotowanie danych testowych
        $this->investor = User::factory()->create([
            'role' => 'investor',
            'kyc_status' => 'verified'
        ]);

        $manager = User::factory()->create([
            'role' => 'manager',
            'kyc_status' => 'verified'
        ]);

        $this->project = Project::factory()->create([
            'owner_id' => $manager->id,
            'status' => 'active',
            'target_amount' => 10000000,
            'min_investment' => 1000000,
            'returns_projection' => 10,
            'category' => 'nieruchomości komercyjne',
            'location' => 'Warszawa'
        ]);

        $this->investment = Investment::create([
            'user_id' => $this->investor->id,
            'project_id' => $this->project->id,
            'amount' => 2000000,
            'status' => Investment::STATUS_INTERESTED,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com',
            'notes' => 'Zainteresowany projektem'
        ]);
    }

    /** @test */
    public function it_has_correct_relations()
    {
        $this->assertInstanceOf(User::class, $this->investment->user);
        $this->assertInstanceOf(Project::class, $this->investment->project);
        $this->assertEquals($this->investor->id, $this->investment->user->id);
        $this->assertEquals($this->project->id, $this->investment->project->id);
    }

    /** @test */
    public function it_can_check_status_correctly()
    {
        // Sprawdzenie statusu "zainteresowany"
        $this->assertTrue($this->investment->isInterested());
        $this->assertFalse($this->investment->isInTalks());
        $this->assertFalse($this->investment->isContractSigned());
        $this->assertFalse($this->investment->isCancelled());
        $this->assertTrue($this->investment->isActive());

        // Zmiana statusu na "w trakcie rozmów"
        $this->investment->status = Investment::STATUS_IN_TALKS;
        $this->investment->save();

        $this->assertFalse($this->investment->isInterested());
        $this->assertTrue($this->investment->isInTalks());
        $this->assertFalse($this->investment->isContractSigned());
        $this->assertFalse($this->investment->isCancelled());
        $this->assertTrue($this->investment->isActive());

        // Zmiana statusu na "umowa podpisana"
        $this->investment->status = Investment::STATUS_CONTRACT_SIGNED;
        $this->investment->save();

        $this->assertFalse($this->investment->isInterested());
        $this->assertFalse($this->investment->isInTalks());
        $this->assertTrue($this->investment->isContractSigned());
        $this->assertFalse($this->investment->isCancelled());
        $this->assertTrue($this->investment->isActive());

        // Zmiana statusu na "anulowana"
        $this->investment->status = Investment::STATUS_CANCELLED;
        $this->investment->save();

        $this->assertFalse($this->investment->isInterested());
        $this->assertFalse($this->investment->isInTalks());
        $this->assertFalse($this->investment->isContractSigned());
        $this->assertTrue($this->investment->isCancelled());
        $this->assertFalse($this->investment->isActive());
    }

    /** @test */
    public function it_calculates_projected_return_value_correctly()
    {
        // Projekt ma zwrot 10%, więc 2000000 * 1.1 = 2200000
        $expectedReturn = 2200000;
        $this->assertEquals($expectedReturn, $this->investment->calculateProjectedReturnValue());
    }

    /** @test */
    public function it_calculates_projected_profit_correctly()
    {
        // Zysk to różnica między zwrotem a kwotą inwestycji: 2200000 - 2000000 = 200000
        $expectedProfit = 200000;
        $this->assertEquals($expectedProfit, $this->investment->calculateProjectedProfit());
    }

    /** @test */
    public function it_changes_status_correctly()
    {
        // Zmiana statusu z "zainteresowany" na "w trakcie rozmów"
        $this->investment->changeStatus(Investment::STATUS_IN_TALKS);
        $this->assertTrue($this->investment->isInTalks());
        
        // Zmiana statusu z "w trakcie rozmów" na "umowa podpisana"
        $this->investment->changeStatus(Investment::STATUS_CONTRACT_SIGNED);
        $this->assertTrue($this->investment->isContractSigned());
        
        // Zmiana statusu z "umowa podpisana" na "anulowana"
        $this->investment->changeStatus(Investment::STATUS_CANCELLED);
        $this->assertTrue($this->investment->isCancelled());
    }

    /** @test */
    public function it_returns_status_list()
    {
        $statusList = Investment::getStatusList();
        
        $this->assertIsArray($statusList);
        $this->assertCount(4, $statusList);
        $this->assertArrayHasKey(Investment::STATUS_INTERESTED, $statusList);
        $this->assertArrayHasKey(Investment::STATUS_IN_TALKS, $statusList);
        $this->assertArrayHasKey(Investment::STATUS_CONTRACT_SIGNED, $statusList);
        $this->assertArrayHasKey(Investment::STATUS_CANCELLED, $statusList);
    }
}
