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
        $admin = User::factory()->create(['role' => 'Administrator']);
        $manager = User::factory()->create(['role' => 'Manager']);
        $investor = User::factory()->create(['role' => 'Investor']);

        // 2. Manager tworzy nowy projekt (status: draft)
        $this->actingAs($manager);
        
        $projectData = [
            'name' => 'Lifecycle Test Project',
            'description' => 'This is a test project for the full lifecycle test',
            'target_amount' => 50000,
            'min_investment' => 1000,
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(37)->format('Y-m-d'),
            'returns_projection' => 12.5,
            'risk_level' => 'medium',
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
        
        // 6. Symulacja finansowania projektu
        $this->actingAs($admin);
        $project->current_amount = $project->target_amount; // W pełni sfinansowany
        $project->save();
        
        // 7. Administrator zmienia status projektu na funded
        $response = $this->patch(route('projects.changeStatus', $project), [
            'status' => 'funded'
        ]);
        $response->assertRedirect(route('projects.show', $project));
        
        $project->refresh();
        $this->assertEquals('funded', $project->status);
        
        // 8. Sprawdź, czy teraz projekt wyświetla się jako sfinansowany
        $this->actingAs($investor);
        $response = $this->get(route('projects.show', $project));
        $response->assertStatus(200);
        $response->assertSee('Sfinansowany');
        
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
        $admin = User::factory()->create(['role' => 'Administrator']);
        $owner = User::factory()->create(['role' => 'Manager']);
        
        // 1. Projekt w fazie szkicu - można edytować wszystkie pola
        $draftProject = Project::factory()->create([
            'status' => 'draft',
            'owner_id' => $owner->id,
            'name' => 'Draft Project',
            'target_amount' => 100000
        ]);
        
        $this->actingAs($owner);
        $response = $this->put(route('projects.update', $draftProject), [
            'name' => 'Updated Draft Project',
            'description' => $draftProject->description,
            'target_amount' => 150000, // Zmieniona kwota docelowa
            'min_investment' => $draftProject->min_investment,
            'start_date' => $draftProject->start_date->format('Y-m-d'),
            'end_date' => $draftProject->end_date->format('Y-m-d'),
            'returns_projection' => $draftProject->returns_projection,
            'risk_level' => $draftProject->risk_level,
        ]);
        
        $response->assertRedirect(route('projects.show', $draftProject));
        $draftProject->refresh();
        $this->assertEquals('Updated Draft Project', $draftProject->name);
        $this->assertEquals(150000, $draftProject->target_amount);
        
        // 2. Administrator zmienia status na aktywny
        $this->actingAs($admin);
        $response = $this->patch(route('projects.changeStatus', $draftProject), [
            'status' => 'active'
        ]);
        
        $draftProject->refresh();
        $this->assertEquals('active', $draftProject->status);
        
        // 3. Projekt aktywny - można edytować niektóre pola, ale nie kwotę docelową 
        // Administrator może zmienić status
        $activeProject = $draftProject; // Teraz projekt jest aktywny
        
        $this->actingAs($admin);
        $response = $this->put(route('projects.update', $activeProject), [
            'name' => 'Updated Active Project',
            'description' => $activeProject->description,
            'target_amount' => $activeProject->target_amount,
            'min_investment' => $activeProject->min_investment,
            'start_date' => $activeProject->start_date->format('Y-m-d'),
            'end_date' => $activeProject->end_date->format('Y-m-d'),
            'returns_projection' => $activeProject->returns_projection,
            'risk_level' => $activeProject->risk_level,
            'status' => 'funded' // Admin może zmienić status
        ]);
        
        $response->assertRedirect(route('projects.show', $activeProject));
        $activeProject->refresh();
        $this->assertEquals('Updated Active Project', $activeProject->name);
        $this->assertEquals('funded', $activeProject->status);
        
        // 4. Projekt sfinansowany - admin nadal może edytować
        $fundedProject = $activeProject; // Teraz projekt jest sfinansowany
        
        $this->actingAs($admin);
        $response = $this->put(route('projects.update', $fundedProject), [
            'name' => 'Updated Funded Project',
            'description' => $fundedProject->description,
            'target_amount' => $fundedProject->target_amount,
            'min_investment' => $fundedProject->min_investment,
            'start_date' => $fundedProject->start_date->format('Y-m-d'),
            'end_date' => $fundedProject->end_date->format('Y-m-d'),
            'returns_projection' => $fundedProject->returns_projection,
            'risk_level' => $fundedProject->risk_level,
            'status' => 'completed' // Admin zmienia status na zakończony
        ]);
        
        $response->assertRedirect(route('projects.show', $fundedProject));
        $fundedProject->refresh();
        $this->assertEquals('Updated Funded Project', $fundedProject->name);
        $this->assertEquals('completed', $fundedProject->status);
    }
}
