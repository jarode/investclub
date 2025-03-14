<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sprawdzający czy administrator ma dostęp do wszystkich operacji
     */
    public function test_admin_has_all_access(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        $project = Project::factory()->create();
        
        $this->assertTrue($admin->can('viewAny', Project::class));
        $this->assertTrue($admin->can('view', $project));
        $this->assertTrue($admin->can('create', Project::class));
        $this->assertTrue($admin->can('update', $project));
        $this->assertTrue($admin->can('delete', $project));
        $this->assertTrue($admin->can('restore', $project));
        $this->assertTrue($admin->can('forceDelete', $project));
        $this->assertTrue($admin->can('changeStatus', $project));
    }
    
    /**
     * Test sprawdzający uprawnienia managera
     */
    public function test_manager_permissions(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        $project = Project::factory()->create();
        $projectOwnedByManager = Project::factory()->create(['owner_id' => $manager->id]);
        
        // Manager może przeglądać i tworzyć projekty
        $this->assertTrue($manager->can('viewAny', Project::class));
        $this->assertTrue($manager->can('view', $project));
        $this->assertTrue($manager->can('create', Project::class));
        
        // Manager może aktualizować projekty, ale nie może usuwać projektów innych osób
        $this->assertTrue($manager->can('update', $project));
        $this->assertFalse($manager->can('delete', $project));
        
        // Manager może usuwać swoje projekty
        $this->assertTrue($manager->can('update', $projectOwnedByManager));
        $this->assertTrue($manager->can('delete', $projectOwnedByManager));
        
        // Manager nie może przywracać, trwale usuwać ani zmieniać statusu projektów
        $this->assertFalse($manager->can('restore', $project));
        $this->assertFalse($manager->can('forceDelete', $project));
        $this->assertFalse($manager->can('changeStatus', $project));
    }
    
    /**
     * Test sprawdzający uprawnienia właściciela projektu
     */
    public function test_project_owner_permissions(): void
    {
        $user = User::factory()->create(['role' => 'Investor']);
        $projectOwnedByUser = Project::factory()->create(['owner_id' => $user->id]);
        $otherProject = Project::factory()->create();
        
        // Właściciel może widzieć swoje projekty
        $this->assertTrue($user->can('view', $projectOwnedByUser));
        
        // Właściciel może aktualizować i usuwać swoje projekty
        $this->assertTrue($user->can('update', $projectOwnedByUser));
        $this->assertTrue($user->can('delete', $projectOwnedByUser));
        
        // Właściciel nie może zmienić statusu, przywrócić ani trwale usunąć swoich projektów
        $this->assertFalse($user->can('changeStatus', $projectOwnedByUser));
        $this->assertFalse($user->can('restore', $projectOwnedByUser));
        $this->assertFalse($user->can('forceDelete', $projectOwnedByUser));
        
        // Użytkownik nie ma dostępu do projektów, których nie jest właścicielem
        $this->assertFalse($user->can('update', $otherProject));
        $this->assertFalse($user->can('delete', $otherProject));
    }
    
    /**
     * Test sprawdzający uprawnienia do przeglądania projektów w różnych statusach
     */
    public function test_viewing_projects_with_different_statuses(): void
    {
        $admin = User::factory()->create(['role' => 'Administrator']);
        $manager = User::factory()->create(['role' => 'Manager']);
        $regularUser = User::factory()->create(['role' => 'Investor']);
        $owner = User::factory()->create(['role' => 'Investor']);
        
        // Projekt w szkicu
        $draftProject = Project::factory()->create([
            'status' => 'draft',
            'owner_id' => $owner->id
        ]);
        
        // Projekty w innych statusach
        $activeProject = Project::factory()->create(['status' => 'active']);
        $fundedProject = Project::factory()->create(['status' => 'funded']);
        $completedProject = Project::factory()->create(['status' => 'completed']);
        
        // Administratorzy i managerowie mogą widzieć projekty w szkicu
        $this->assertTrue($admin->can('view', $draftProject));
        $this->assertTrue($manager->can('view', $draftProject));
        
        // Właściciel może widzieć swój projekt w szkicu
        $this->assertTrue($owner->can('view', $draftProject));
        
        // Zwykły użytkownik nie może widzieć projektów w szkicu
        $this->assertFalse($regularUser->can('view', $draftProject));
        
        // Wszyscy użytkownicy mogą widzieć projekty w innych statusach
        $this->assertTrue($admin->can('view', $activeProject));
        $this->assertTrue($manager->can('view', $activeProject));
        $this->assertTrue($owner->can('view', $activeProject));
        $this->assertTrue($regularUser->can('view', $activeProject));
        
        $this->assertTrue($admin->can('view', $fundedProject));
        $this->assertTrue($manager->can('view', $fundedProject));
        $this->assertTrue($owner->can('view', $fundedProject));
        $this->assertTrue($regularUser->can('view', $fundedProject));
        
        $this->assertTrue($admin->can('view', $completedProject));
        $this->assertTrue($manager->can('view', $completedProject));
        $this->assertTrue($owner->can('view', $completedProject));
        $this->assertTrue($regularUser->can('view', $completedProject));
    }
    
    /**
     * Test sprawdzający czy zwykli użytkownicy nie mogą tworzyć projektów
     */
    public function test_regular_users_cannot_create_projects(): void
    {
        $investor = User::factory()->create(['role' => 'Investor']);
        $accountant = User::factory()->create(['role' => 'Accountant']);
        $regularUser = User::factory()->create(['role' => 'Regular User']);
        
        $this->assertFalse($investor->can('create', Project::class));
        $this->assertFalse($accountant->can('create', Project::class));
        $this->assertFalse($regularUser->can('create', Project::class));
    }
}
