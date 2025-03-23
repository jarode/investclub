<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class InvestmentControllerTest extends TestCase
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
            'returns_projection' => 10,
            'category' => 'nieruchomości komercyjne',
            'location' => 'Warszawa'
        ]);

        // Tworzenie dodatkowego aktywnego projektu
        $this->anotherProject = Project::factory()->create([
            'owner_id' => $this->manager->id,
            'status' => 'active',
            'target_amount' => 20000000,
            'min_investment' => 2000000,
            'returns_projection' => 15,
            'category' => 'nieruchomości mieszkaniowe',
            'location' => 'Gdynia'
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

    #[Test]
    public function index_displays_investments_for_admin()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('investments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('investments.index');
        $response->assertViewHas('investments');
        
        // Pobieramy inwestycje z widoku (uwzględniając paginację)
        $investments = $response->viewData('investments');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $investments);
        $this->assertCount(4, $investments->items());
    }

    /** @test */
    public function index_displays_only_own_investments_for_investor()
    {
        // Utwórz inwestycję innego inwestora
        Investment::create([
            'user_id' => $this->anotherInvestor->id,
            'project_id' => $this->project->id,
            'amount' => 2000000,
            'status' => Investment::STATUS_INTERESTED,
            'contact_preference' => 'email',
            'contact_details' => 'inny@example.com'
        ]);

        $this->actingAs($this->investor);

        $response = $this->get(route('investments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('investments.index');
        $response->assertViewHas('investments');
        // Powinien widzieć tylko swoje 4 inwestycje
        $this->assertCount(4, $response->viewData('investments'));
    }

    /** @test */
    public function index_filters_investments_by_status()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('investments.index', ['status' => 'interested']));

        $response->assertStatus(200);
        $response->assertViewIs('investments.index');
        $response->assertViewHas('investments');
        
        // Pobieramy inwestycje z widoku (uwzględniając paginację)
        $investments = $response->viewData('investments');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $investments);
        $this->assertCount(1, $investments->items());
    }

    /** @test */
    public function index_filters_investments_by_project()
    {
        $this->actingAs($this->admin);

        // Dodaj inwestycję w innym projekcie
        Investment::create([
            'user_id' => $this->investor->id,
            'project_id' => $this->anotherProject->id,
            'amount' => 2500000,
            'status' => Investment::STATUS_INTERESTED,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com'
        ]);

        $response = $this->get(route('investments.index', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertViewIs('investments.index');
        $response->assertViewHas('investments');
        
        // Pobieramy inwestycje z widoku (uwzględniając paginację)
        $investments = $response->viewData('investments');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $investments);
        $this->assertCount(4, $investments->items());
    }

    /** @test */
    public function create_displays_investment_form()
    {
        $this->actingAs($this->investor);

        $response = $this->get(route('investments.create'));

        $response->assertStatus(200);
        $response->assertViewIs('investments.create');
        $response->assertViewHas('availableProjects');
    }

    /** @test */
    public function create_with_project_id_displays_investment_form_for_specific_project()
    {
        $this->actingAs($this->investor);

        $response = $this->get(route('investments.create', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertViewIs('investments.create');
        $response->assertViewHas('project');
    }

    /** @test */
    public function store_creates_new_investment()
    {
        $this->actingAs($this->investor);

        $response = $this->post(route('investments.store'), [
            'project_id' => $this->project->id,
            'amount' => 2000000,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com',
            'notes' => 'Zainteresowany projektem'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Sprawdzenie, czy inwestycja została utworzona
        $this->assertDatabaseHas('investments', [
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
    public function store_validates_minimum_investment_amount()
    {
        $this->actingAs($this->investor);

        $response = $this->post(route('investments.store'), [
            'project_id' => $this->project->id,
            'amount' => 500000, // Poniżej minimalnej kwoty 1000000
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com',
            'notes' => 'Test investment'
        ]);

        $response->assertRedirect(route('investments.create', ['project_id' => $this->project->id]));
        $response->assertSessionHas('error');
        
        // Sprawdzenie, czy inwestycja NIE została utworzona
        $this->assertDatabaseMissing('investments', [
            'user_id' => $this->investor->id,
            'project_id' => $this->project->id,
            'amount' => 500000
        ]);
    }

    /** @test */
    public function store_requires_verified_kyc()
    {
        // Utworzenie inwestora bez weryfikacji KYC
        $unverifiedInvestor = User::factory()->create([
            'role' => 'investor',
            'kyc_status' => 'pending'
        ]);

        $this->actingAs($unverifiedInvestor);

        $response = $this->post(route('investments.store'), [
            'project_id' => $this->project->id,
            'amount' => 2000000,
            'contact_preference' => 'email',
            'contact_details' => 'unverified@example.com',
            'notes' => 'Test investment'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Sprawdzenie, czy inwestycja NIE została utworzona
        $this->assertDatabaseMissing('investments', [
            'user_id' => $unverifiedInvestor->id
        ]);
    }

    /** @test */
    public function show_displays_investment_details()
    {
        $this->actingAs($this->investor);

        $response = $this->get(route('investments.show', $this->interestedInvestment));

        $response->assertStatus(200);
        $response->assertViewIs('investments.show');
        $response->assertViewHas('investment');
        $response->assertSee($this->interestedInvestment->amount);
        $response->assertSee($this->interestedInvestment->status);
    }

    /** @test */
    public function update_changes_investment_status()
    {
        $this->actingAs($this->manager);

        $response = $this->patch(route('investments.update', $this->interestedInvestment), [
            'status' => Investment::STATUS_IN_TALKS
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->interestedInvestment->refresh();
        $this->assertEquals(Investment::STATUS_IN_TALKS, $this->interestedInvestment->status);
    }

    /** @test */
    public function destroy_cancels_investment()
    {
        $this->actingAs($this->investor);

        $response = $this->delete(route('investments.destroy', $this->interestedInvestment));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->interestedInvestment->refresh();
        $this->assertEquals(Investment::STATUS_CANCELLED, $this->interestedInvestment->status);
    }
}
