<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Każdy zalogowany użytkownik może przeglądać listę projektów
        // Projekty draft są widoczne tylko dla właścicieli, administratorów i managerów
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Projekty aktywne lub zakończone mogą zobaczyć wszyscy zalogowani użytkownicy
        if (in_array($project->status, ['active', 'completed'])) {
            // Ekskluzywne projekty tylko dla użytkowników premium
            if ($project->is_exclusive) {
                return $user->hasActiveSubscription() && 
                       in_array($user->plan_type, ['premium-investor', 'premium-owner']);
            }
            return true;
        }
        
        // Projekty w trybie draft mogą zobaczyć tylko właściciele, administratorzy i managerowie
        return $project->owner_id === $user->id || 
               $user->isAdmin() || 
               $user->isManager();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tylko użytkownicy z planem O-Premium lub administratorzy/managerowie mogą tworzyć projekty
        return $user->canManageProjects();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Nie można edytować zakończonych projektów
        if ($project->status === 'completed') {
            return false;
        }
        
        // Właściciel projektu, administratorzy i managerowie mogą aktualizować projekt
        return $project->owner_id === $user->id || 
               $user->isAdmin() || 
               $user->isManager();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Tylko właściciele projektów i administratorzy mogą usuwać projekty
        return $project->owner_id === $user->id || 
               $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        // Tylko administratorzy mogą przywracać usunięte projekty
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        // Tylko administratorzy mogą trwale usuwać projekty
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can change the status of the project.
     */
    public function changeStatus(User $user, Project $project): bool
    {
        // Tylko administratorzy mogą zmieniać status projektu
        return $user->isAdmin();
    }
}
