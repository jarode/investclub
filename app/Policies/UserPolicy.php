<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Metoda uruchamiana przed wszystkimi innymi metodami.
     * Jeśli użytkownik jest administratorem, to ma dostęp do wszystkich operacji.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        return null; // null oznacza kontynuowanie sprawdzania w pozostałych metodach
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tylko administrator i manager mogą przeglądać listę użytkowników
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Użytkownik może zobaczyć swój własny profil lub jeśli ma rolę managera
        return $user->id === $model->id || $user->hasRole('manager');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tylko administrator i manager mogą tworzyć użytkowników
        return $user->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Użytkownik może edytować swój własny profil, a manager może edytować wszystkie oprócz adminów
        return $user->id === $model->id || 
               ($user->hasRole('manager') && !$model->isAdmin());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Tylko administrator może usuwać użytkowników, i nie może usunąć sam siebie
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Tylko administrator może przywracać użytkowników
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Tylko administrator może trwale usuwać użytkowników
        return $user->isAdmin();
    }
    
    /**
     * Determine whether the user can manage investments.
     */
    public function manageInvestments(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'investor']);
    }
    
    /**
     * Determine whether the user can view sensitive financial data.
     */
    public function viewFinancialData(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'accountant']);
    }
}
