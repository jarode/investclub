<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test sprawdzający czy niezalogowany użytkownik jest przekierowywany do strony logowania
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('projects.show', $project));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('projects.create'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('projects.edit', $project));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test sprawdzający dostęp do listy projektów
     */
    public function test_users_can_view_projects_index(): void
    {
        $user = User::factory()->create();
        $projects = Project::factory()->count(3)->create();

        $response = $this->actingAs($user)
                         ->get(route('projects.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('projects.index');
        $response->assertViewHas('projects');
    }

    /**
     * Test sprawdzający dostęp do widoku szczegółów projektu aktywnego
     */
    public function test_users_can_view_active_project_details(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user)
                         ->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertViewIs('projects.show');
        $response->assertViewHas('project');
    }

    /**
     * Test sprawdzający ograniczenie dostępu do projektów w trybie szkicu
     */
    public function test_draft_projects_access_is_restricted(): void
    {
        $regularUser = User::factory()->create(['role' => 'Investor']);
        $admin = User::factory()->create(['role' => 'Administrator']);
        $owner = User::factory()->create(['role' => 'Investor']);

        $draftProject = Project::factory()->create([
            'status' => 'draft',
            'owner_id' => $owner->id
        ]);

        // Zwykły użytkownik nie ma dostępu
        $response = $this->actingAs($regularUser)
                         ->get(route('projects.show', $draftProject));
        $response->assertForbidden();

        // Admin ma dostęp
        $response = $this->actingAs($admin)
                         ->get(route('projects.show', $draftProject));
        $response->assertStatus(200);

        // Właściciel ma dostęp
        $response = $this->actingAs($owner)
                         ->get(route('projects.show', $draftProject));
        $response->assertStatus(200);
    }

    /**
     * Test sprawdzający czy tylko administratorzy i managerowie mogą utworzyć projekt
     */
    public function test_only_admins_and_managers_can_create_projects(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        $manager = User::factory()->create(['role' => 'Manager']);
        $regularUser = User::factory()->create(['role' => 'Investor']);

        // Admin ma dostęp do formularza tworzenia
        $response = $this->actingAs($admin)
                         ->get(route('projects.create'));
        $response->assertStatus(200);

        // Manager ma dostęp do formularza tworzenia
        $response = $this->actingAs($manager)
                         ->get(route('projects.create'));
        $response->assertStatus(200);

        // Zwykły użytkownik nie ma dostępu
        $response = $this->actingAs($regularUser)
                         ->get(route('projects.create'));
        $response->assertForbidden();
    }

    /**
     * Test sprawdzający tworzenie projektu przez administratora
     */
    public function test_admin_can_store_new_project(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);

        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project description',
            'target_amount' => 100000,
            'min_investment' => 1000,
            'start_date' => now()->addDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(35)->format('Y-m-d'),
            'returns_projection' => 15,
            'risk_level' => 'medium',
        ];

        $response = $this->actingAs($admin)
                         ->post(route('projects.store'), $projectData);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'owner_id' => $admin->id,
            'status' => 'draft'
        ]);
    }

    /**
     * Test sprawdzający aktualizację projektu przez właściciela
     */
    public function test_owner_can_update_project(): void
    {
        $owner = User::factory()->create(['role' => 'Investor']);
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'Original Project Name'
        ]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => $project->description,
            'target_amount' => $project->target_amount,
            'min_investment' => $project->min_investment,
            'start_date' => $project->start_date->format('Y-m-d'),
            'end_date' => $project->end_date->format('Y-m-d'),
            'returns_projection' => $project->returns_projection,
            'risk_level' => $project->risk_level,
        ];

        $response = $this->actingAs($owner)
                         ->put(route('projects.update', $project), $updateData);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name'
        ]);
    }

    /**
     * Test sprawdzający czy administrator może zmienić status projektu
     */
    public function test_admin_can_change_project_status(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        $project = Project::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($admin)
                         ->patch(route('projects.changeStatus', $project), [
                             'status' => 'active'
                         ]);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => 'active'
        ]);
    }

    /**
     * Test sprawdzający czy manager nie może zmienić statusu projektu
     */
    public function test_manager_cannot_change_project_status(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        $project = Project::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($manager)
                         ->patch(route('projects.changeStatus', $project), [
                             'status' => 'active'
                         ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => 'draft'  // Status nie powinien się zmienić
        ]);
    }

    /**
     * Test sprawdzający czy właściciel może usunąć swój projekt
     */
    public function test_owner_can_delete_project(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)
                         ->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $this->assertSoftDeleted($project);
    }

    /**
     * Test sprawdzający filtry na liście projektów
     */
    public function test_projects_can_be_filtered(): void
    {
        $user = User::factory()->create();
        
        // Tworzymy projekty o różnych statusach
        $draftProject = Project::factory()->create(['status' => 'draft']);
        $activeProject = Project::factory()->create(['status' => 'active']);
        $fundedProject = Project::factory()->create(['status' => 'funded']);
        
        // Tworzymy projekty o różnych poziomach ryzyka
        $lowRiskProject = Project::factory()->create(['risk_level' => 'low', 'status' => 'active']);
        $mediumRiskProject = Project::factory()->create(['risk_level' => 'medium', 'status' => 'active']);
        $highRiskProject = Project::factory()->create(['risk_level' => 'high', 'status' => 'active']);

        // Test filtrowania po statusie
        $response = $this->actingAs($user)
                         ->get(route('projects.index', ['status' => 'active']));
        
        $response->assertStatus(200);
        $response->assertViewHas('projects', function ($projects) use ($activeProject, $lowRiskProject, $mediumRiskProject, $highRiskProject) {
            return $projects->contains($activeProject) && 
                   $projects->contains($lowRiskProject) && 
                   $projects->contains($mediumRiskProject) && 
                   $projects->contains($highRiskProject) && 
                   $projects->count() === 4;
        });

        // Test filtrowania po poziomie ryzyka
        $response = $this->actingAs($user)
                         ->get(route('projects.index', ['risk_level' => 'high']));
        
        $response->assertStatus(200);
        $response->assertViewHas('projects', function ($projects) use ($highRiskProject) {
            return $projects->contains($highRiskProject) && $projects->count() === 1;
        });
    }
    
    /**
     * Test walidacji formularza tworzenia projektu
     */
    public function test_project_creation_validation(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        
        // Brakujące wymagane pola
        $response = $this->actingAs($admin)
                         ->post(route('projects.store'), []);
        
        $response->assertSessionHasErrors(['name', 'description', 'target_amount', 'min_investment', 'start_date', 'end_date', 'returns_projection', 'risk_level']);
        
        // Nieprawidłowa data zakończenia (wcześniejsza niż data rozpoczęcia)
        $response = $this->actingAs($admin)
                         ->post(route('projects.store'), [
                             'name' => 'Test Project',
                             'description' => 'Description',
                             'target_amount' => 10000,
                             'min_investment' => 1000,
                             'start_date' => now()->addDays(10)->format('Y-m-d'),
                             'end_date' => now()->addDays(5)->format('Y-m-d'),  // Wcześniejsza niż start_date
                             'returns_projection' => 15,
                             'risk_level' => 'medium',
                         ]);
        
        $response->assertSessionHasErrors('end_date');
        
        // Nieprawidłowy poziom ryzyka
        $response = $this->actingAs($admin)
                         ->post(route('projects.store'), [
                             'name' => 'Test Project',
                             'description' => 'Description',
                             'target_amount' => 10000,
                             'min_investment' => 1000,
                             'start_date' => now()->addDays(5)->format('Y-m-d'),
                             'end_date' => now()->addDays(35)->format('Y-m-d'),
                             'returns_projection' => 15,
                             'risk_level' => 'invalid',
                         ]);
        
        $response->assertSessionHasErrors('risk_level');
    }
}
