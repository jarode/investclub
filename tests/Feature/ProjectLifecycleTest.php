<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectLifecycleTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test pełnego cyklu życia projektu od utworzenia do zakończenia.
     */
    public function test_project_full_lifecycle(): void
    {
        // 1. Przygotowanie użytkowników
        $admin = User::factory()->create([
            'role' => 'admin',
            'kyc_status' => 'verified'
        ]);
        
        $manager = User::factory()->create([
            'role' => 'manager',
            'kyc_status' => 'verified'
        ]);
        
        $investor = User::factory()->create([
            'role' => 'investor',
            'kyc_status' => 'verified'
        ]);

        // 2. Manager tworzy nowy projekt (status: draft)
        $this->actingAs($manager);
        
        $projectData = [
            'name' => 'Lifecycle Test Project',
            'description' => 'This is a test project for the full lifecycle test',
            'target_amount' => 10000000,
            'min_investment' => 1000000,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(37)->format('Y-m-d'),
            'returns_projection' => 12.5,
            'risk_level' => 'medium',
            'category' => 'nieruchomości komercyjne',
            'location' => 'Warszawa'
        ];
        
        $response = $this->post(route('projects.store'), $projectData);
        $response->assertRedirect();
        
        // Pobierz utworzony projekt
        $project = Project::where('name', 'Lifecycle Test Project')->first();
        $this->assertNotNull($project);
        $this->assertEquals('draft', $project->status);
        $this->assertEquals($manager->id, $project->owner_id);
        
        // 3. Sprawdź, czy zwykły inwestor nie może zobaczyć projektu w trybie draft
        $this->actingAs($investor);
        $response = $this->get(route('projects.show', $project));
        $response->assertForbidden();
        
        // 4. Administrator zmienia status projektu na active
        $this->actingAs($admin);
        $response = $this->patch(route('projects.changeStatus', $project), [
            'status' => 'active'
        ]);
        $response->assertRedirect(route('projects.show', $project));
        
        $project->refresh();
        $this->assertEquals('active', $project->status);
        
        // 5. Zwykły inwestor teraz może zobaczyć aktywny projekt
        $this->actingAs($investor);
        $response = $this->get(route('projects.show', $project));
        $response->assertStatus(200);
        $response->assertViewIs('projects.show');
        
        // 6. Inwestor wyraża zainteresowanie projektem
        $response = $this->post(route('investments.store'), [
            'project_id' => $project->id,
            'amount' => 2000000,
            'contact_preference' => 'email',
            'contact_details' => 'inwestor@example.com',
            'notes' => 'Zainteresowany projektem'
        ]);
        $response->assertRedirect();
        
        // 7. Manager rozpoczyna rozmowy z inwestorem
        $this->actingAs($manager);
        $investment = $project->investments()->first();
        $response = $this->patch(route('investments.update', $investment), [
            'status' => 'in_talks'
        ]);
        $response->assertRedirect();
        
        $investment->refresh();
        $this->assertEquals('in_talks', $investment->status);
        
        // 8. Manager oznacza umowę jako podpisaną
        $response = $this->patch(route('investments.update', $investment), [
            'status' => 'contract_signed'
        ]);
        $response->assertRedirect();
        
        $investment->refresh();
        $this->assertEquals('contract_signed', $investment->status);
        
        // 9. Administrator kończy projekt zmieniając status na completed
        $this->actingAs($admin);
        $response = $this->patch(route('projects.changeStatus', $project), [
            'status' => 'completed'
        ]);
        $response->assertRedirect(route('projects.show', $project));
        
        $project->refresh();
        $this->assertEquals('completed', $project->status);
        
        // 10. Sprawdź, czy projekt wyświetla się jako zakończony
        $this->actingAs($investor);
        $response = $this->get(route('projects.show', $project));
        $response->assertStatus(200);
        $response->assertSee('Zakończony');
        
        // 11. Sprawdź, czy projekt pojawia się na liście projektów z odpowiednim statusem
        $response = $this->get(route('projects.index', ['status' => 'completed']));
        $response->assertStatus(200);
        $response->assertSee('Lifecycle Test Project');
        
        // 12. Administrator może usunąć zakończony projekt
        $this->actingAs($admin);
        $response = $this->delete(route('projects.destroy', $project));
        $response->assertRedirect(route('projects.index'));
        
        $this->assertSoftDeleted($project);
    }
    
    /**
     * Test sprawdzający edycję projektu w różnych etapach cyklu życia
     */
    public function test_project_editing_in_different_lifecycle_stages(): void
    {
        // Przygotowanie
        $admin = User::factory()->create([
            'role' => 'admin',
            'kyc_status' => 'verified'
        ]);
        
        $owner = User::factory()->create([
            'role' => 'manager',
            'kyc_status' => 'verified'
        ]);
        
        // 1. Projekt w fazie szkicu - można edytować wszystkie pola
        $draftProject = Project::factory()->create([
            'status' => 'draft',
            'owner_id' => $owner->id,
            'name' => 'Draft Project',
            'target_amount' => 10000000,
            'min_investment' => 1000000,
            'category' => 'nieruchomości komercyjne',
            'location' => 'Warszawa'
        ]);
        
        $this->actingAs($owner);
        $response = $this->put(route('projects.update', $draftProject), [
            'name' => 'Updated Draft Project',
            'description' => $draftProject->description,
            'target_amount' => 15000000, // Zmieniona kwota docelowa
            'min_investment' => $draftProject->min_investment,
            'start_date' => $draftProject->start_date->format('Y-m-d'),
            'end_date' => $draftProject->end_date->format('Y-m-d'),
            'returns_projection' => $draftProject->returns_projection,
            'risk_level' => $draftProject->risk_level,
            'category' => $draftProject->category,
            'location' => $draftProject->location
        ]);
        
        $response->assertRedirect(route('projects.show', $draftProject));
        $draftProject->refresh();
        $this->assertEquals('Updated Draft Project', $draftProject->name);
        $this->assertEquals(15000000, $draftProject->target_amount);
        
        // 2. Administrator zmienia status na aktywny
        $this->actingAs($admin);
        $response = $this->patch(route('projects.changeStatus', $draftProject), [
            'status' => 'active'
        ]);
        
        $draftProject->refresh();
        $this->assertEquals('active', $draftProject->status);
        
        // 3. Projekt aktywny - można edytować niektóre pola, ale nie kwotę docelową 
        $activeProject = $draftProject; // Teraz projekt jest aktywny
        
        $this->actingAs($owner);
        $response = $this->put(route('projects.update', $activeProject), [
            'name' => 'Updated Active Project',
            'description' => $activeProject->description,
            'target_amount' => $activeProject->target_amount, // Nie można zmienić
            'min_investment' => $activeProject->min_investment, // Nie można zmienić
            'start_date' => $activeProject->start_date->format('Y-m-d'),
            'end_date' => $activeProject->end_date->format('Y-m-d'),
            'returns_projection' => $activeProject->returns_projection,
            'risk_level' => $activeProject->risk_level,
            'category' => $activeProject->category,
            'location' => $activeProject->location
        ]);
        
        $response->assertRedirect(route('projects.show', $activeProject));
        $activeProject->refresh();
        $this->assertEquals('Updated Active Project', $activeProject->name);
        $this->assertEquals(15000000, $activeProject->target_amount); // Kwota nie powinna się zmienić
        
        // 4. Administrator kończy projekt
        $this->actingAs($admin);
        $response = $this->patch(route('projects.changeStatus', $activeProject), [
            'status' => 'completed'
        ]);
        
        $response->assertRedirect(route('projects.show', $activeProject));
        $activeProject->refresh();
        $this->assertEquals('completed', $activeProject->status);
        
        // 5. Projekt zakończony - nie można edytować
        $completedProject = $activeProject;
        
        $this->actingAs($owner);
        $response = $this->put(route('projects.update', $completedProject), [
            'name' => 'Try to Update Completed Project',
            'description' => $completedProject->description,
            'target_amount' => $completedProject->target_amount,
            'min_investment' => $completedProject->min_investment,
            'start_date' => $completedProject->start_date->format('Y-m-d'),
            'end_date' => $completedProject->end_date->format('Y-m-d'),
            'returns_projection' => $completedProject->returns_projection,
            'risk_level' => $completedProject->risk_level,
            'category' => $completedProject->category,
            'location' => $completedProject->location
        ]);
        
        $response->assertForbidden();
        $completedProject->refresh();
        $this->assertEquals('Updated Active Project', $completedProject->name); // Nazwa nie powinna się zmienić
    }
}
