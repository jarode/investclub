<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;
    private User $investor;
    private User $anotherInvestor;
    private Project $project;
    private Investment $interestedInvestment;
    private Investment $inTalksInvestment;
    private Investment $contractSignedInvestment;
    private Investment $cancelledInvestment;

    protected function setUp(): void
    {
        parent::setUp();

        // Tworzenie użytkowników z różnymi rolami
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'kyc_status' => 'verified'
        ]);
        
        $this->manager = User::factory()->create([
            'role' => 'manager',
            'kyc_status' => 'verified'
        ]);
        
        $this->investor = User::factory()->create([
            'role' => 'investor',
            'kyc_status' => 'verified'
        ]);
        
        $this->anotherInvestor = User::factory()->create([
            'role' => 'investor',
            'kyc_status' => 'verified'
        ]);

        // Tworzenie projektu
        $this->project = Project::factory()->create([
            'owner_id' => $this->manager->id,
            'status' => 'active',
            'target_amount' => 10000000,
            'min_investment' => 1000000,
            'category' => 'nieruchomości komercyjne',
            'location' => 'Warszawa'
        ]);

        // Tworzenie inwestycji w różnych statusach
        $this->interestedInvestment = Investment::create([
            'user_id' => $this->investor->id,
            'project_id' => $this->project->id,
            'amount' => 2000000,
            'status' => Investment::STATUS_INTERESTED,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com',
            'notes' => 'Zainteresowany projektem'
        ]);

        $this->inTalksInvestment = Investment::create([
            'user_id' => $this->investor->id,
            'project_id' => $this->project->id,
            'amount' => 3000000,
            'status' => Investment::STATUS_IN_TALKS,
            'contact_preference' => 'phone',
            'contact_details' => '+48123456789'
        ]);

        $this->contractSignedInvestment = Investment::create([
            'user_id' => $this->investor->id,
            'project_id' => $this->project->id,
            'amount' => 4000000,
            'status' => Investment::STATUS_CONTRACT_SIGNED,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com'
        ]);

        $this->cancelledInvestment = Investment::create([
            'user_id' => $this->investor->id,
            'project_id' => $this->project->id,
            'amount' => 5000000,
            'status' => Investment::STATUS_CANCELLED,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com',
            'notes' => 'Anulowane przez inwestora'
        ]);
    }

    /** @test */
    public function admin_can_do_everything()
    {
        $this->actingAs($this->admin);
        
        $this->assertTrue($this->admin->can('viewAny', Investment::class));
        $this->assertTrue($this->admin->can('view', $this->interestedInvestment));
        $this->assertTrue($this->admin->can('create', Investment::class));
        $this->assertTrue($this->admin->can('update', $this->interestedInvestment));
        $this->assertTrue($this->admin->can('delete', $this->interestedInvestment));
        $this->assertTrue($this->admin->can('changeStatus', $this->interestedInvestment));
    }

    /** @test */
    public function manager_can_view_and_manage_project_investments()
    {
        $this->actingAs($this->manager);
        
        $this->assertTrue($this->manager->can('viewAny', Investment::class));
        $this->assertTrue($this->manager->can('view', $this->interestedInvestment));
        $this->assertFalse($this->manager->can('create', Investment::class));
        $this->assertTrue($this->manager->can('changeStatus', $this->interestedInvestment));
        
        // Manager może zmieniać status inwestycji w swoich projektach
        $this->assertTrue($this->manager->can('update', $this->interestedInvestment));
        $this->assertTrue($this->manager->can('update', $this->inTalksInvestment));
        
        // Manager nie może anulować inwestycji
        $this->assertFalse($this->manager->can('delete', $this->interestedInvestment));
    }

    /** @test */
    public function investor_can_only_access_their_own_investments()
    {
        $this->actingAs($this->investor);
        
        $this->assertTrue($this->investor->can('viewAny', Investment::class));
        $this->assertTrue($this->investor->can('view', $this->interestedInvestment));
        $this->assertTrue($this->investor->can('create', Investment::class));
        
        // Inwestor może edytować tylko swoje inwestycje w statusie "zainteresowany"
        $this->assertTrue($this->investor->can('update', $this->interestedInvestment));
        $this->assertFalse($this->investor->can('update', $this->inTalksInvestment));
        
        // Inwestor może anulować swoje inwestycje w statusie "zainteresowany" lub "w trakcie rozmów"
        $this->assertTrue($this->investor->can('delete', $this->interestedInvestment));
        $this->assertTrue($this->investor->can('delete', $this->inTalksInvestment));
        $this->assertFalse($this->investor->can('delete', $this->contractSignedInvestment));
        
        // Inwestor nie może zmieniać statusu inwestycji
        $this->assertFalse($this->investor->can('changeStatus', $this->interestedInvestment));
        
        // Utwórzmy inwestycję innego inwestora
        $otherInvestment = Investment::create([
            'user_id' => $this->anotherInvestor->id,
            'project_id' => $this->project->id,
            'amount' => 2000000,
            'status' => Investment::STATUS_INTERESTED,
            'contact_preference' => 'email',
            'contact_details' => 'inny@example.com'
        ]);
        
        // Inwestor nie może przeglądać, edytować ani anulować inwestycji innych inwestorów
        $this->assertFalse($this->investor->can('view', $otherInvestment));
        $this->assertFalse($this->investor->can('update', $otherInvestment));
        $this->assertFalse($this->investor->can('delete', $otherInvestment));
    }

    /** @test */
    public function investments_can_only_be_updated_when_interested()
    {
        $this->actingAs($this->investor);
        
        // Inwestor może edytować tylko swoje inwestycje w statusie "zainteresowany"
        $this->assertTrue($this->investor->can('update', $this->interestedInvestment));
        $this->assertFalse($this->investor->can('update', $this->inTalksInvestment));
        $this->assertFalse($this->investor->can('update', $this->contractSignedInvestment));
        $this->assertFalse($this->investor->can('update', $this->cancelledInvestment));
    }

    /** @test */
    public function investments_can_only_be_cancelled_when_interested_or_in_talks()
    {
        $this->actingAs($this->investor);
        
        // Inwestor może anulować tylko swoje inwestycje w statusie "zainteresowany" lub "w trakcie rozmów"
        $this->assertTrue($this->investor->can('delete', $this->interestedInvestment));
        $this->assertTrue($this->investor->can('delete', $this->inTalksInvestment));
        $this->assertFalse($this->investor->can('delete', $this->contractSignedInvestment));
        $this->assertFalse($this->investor->can('delete', $this->cancelledInvestment));
    }

    /** @test */
    public function investor_needs_verified_kyc_to_create_investment()
    {
        // Utworzenie inwestora bez weryfikacji KYC
        $unverifiedInvestor = User::factory()->create([
            'role' => 'investor',
            'kyc_status' => 'pending'
        ]);

        $this->actingAs($unverifiedInvestor);
        
        $this->assertFalse($unverifiedInvestor->can('create', Investment::class));
    }
}
