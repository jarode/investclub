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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Projekty aktywne, sfinansowane lub zakończone mogą zobaczyć wszyscy zalogowani użytkownicy
        if (in_array($project->status, ['active', 'funded', 'completed'])) {
            return true;
        }
        
        // Projekty w trybie draft mogą zobaczyć tylko właściciele, administratorzy i managerowie
        return $project->owner_id === $user->id || 
               $user->hasRole('Administrator') || 
               $user->hasRole('Manager');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tylko administratorzy i managerowie mogą tworzyć projekty
        return $user->hasAnyRole(['Administrator', 'Manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Właściciel projektu, administratorzy i managerowie mogą aktualizować projekt
        return $project->owner_id === $user->id || 
               $user->hasRole('Administrator') || 
               $user->hasRole('Manager');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Tylko właściciele projektów i administratorzy mogą usuwać projekty
        return $project->owner_id === $user->id || 
               $user->hasRole('Administrator');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        // Tylko administratorzy mogą przywracać usunięte projekty
        return $user->hasRole('Administrator');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        // Tylko administratorzy mogą trwale usuwać projekty
        return $user->hasRole('Administrator');
    }
    
    /**
     * Determine whether the user can change the status of the project.
     */
    public function changeStatus(User $user, Project $project): bool
    {
        // Tylko administratorzy mogą zmieniać status projektu
        return $user->hasRole('Administrator');
    }
}
