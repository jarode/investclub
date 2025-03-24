<?php

namespace App\Policies;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class InvestmentPolicy
{
    use HandlesAuthorization;

    /**
     * Metoda uruchamiana przed wszystkimi innymi metodami.
     * Jeśli użytkownik jest administratorem, to ma dostęp do wszystkich operacji.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            Log::info("InvestmentPolicy@before - dostęp przyznany dla administratora", [
                'user_id' => $user->id,
                'ability' => $ability
            ]);
            return true;
        }
        
        return null; // null oznacza kontynuowanie sprawdzania w pozostałych metodach
    }
    
    /**
     * Określa czy użytkownik może przeglądać listę inwestycji.
     */
    public function viewAny(User $user): bool
    {
        // Każdy zalogowany użytkownik może przeglądać listę swoich inwestycji
        return true;
    }
    
    /**
     * Określa czy użytkownik może przeglądać szczegóły konkretnej inwestycji.
     */
    public function view(User $user, Investment $investment): bool
    {
        Log::info('InvestmentPolicy@view - sprawdzanie uprawnień', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'investment_id' => $investment->id,
            'investment_user_id' => $investment->user_id,
            'project_id' => $investment->project_id,
            'project_owner_id' => $investment->project->owner_id,
            'is_admin' => $user->isAdmin() ? 'TAK' : 'NIE',
            'is_manager' => $user->isManager() ? 'TAK' : 'NIE',
            'is_investment_owner' => ($user->id === $investment->user_id) ? 'TAK' : 'NIE',
            'is_project_owner' => ($user->id === $investment->project->owner_id) ? 'TAK' : 'NIE'
        ]);

        // Administrator i manager mogą przeglądać wszystkie inwestycje
        if ($user->isAdmin() || $user->isManager()) {
            Log::info('InvestmentPolicy@view - dostęp przyznany (admin/manager)');
            return true;
        }

        // Właściciel projektu może przeglądać inwestycje w swoim projekcie
        if ($user->id === $investment->project->owner_id) {
            Log::info('InvestmentPolicy@view - dostęp przyznany (właściciel projektu)');
            return true;
        }

        // Użytkownik może przeglądać tylko swoje inwestycje
        $result = $user->id === $investment->user_id;
        Log::info('InvestmentPolicy@view - wynik dla inwestora: ' . ($result ? 'TAK' : 'NIE'));
        return $result;
    }
    
    /**
     * Określa czy użytkownik może tworzyć nowe inwestycje.
     */
    public function create(User $user): bool
    {
        // Tylko zweryfikowani użytkownicy z aktywną subskrypcją mogą tworzyć inwestycje
        return $user->kyc_status === 'verified' && $user->hasActiveSubscription();
    }
    
    /**
     * Określa czy użytkownik może aktualizować inwestycję.
     */
    public function update(User $user, Investment $investment): bool
    {
        // Administrator i manager mogą edytować wszystkie inwestycje
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        // Użytkownik może edytować tylko swoje inwestycje w statusie "zainteresowany"
        return $user->id === $investment->user_id && $investment->isInterested();
    }
    
    /**
     * Określa czy użytkownik może anulować inwestycję.
     */
    public function delete(User $user, Investment $investment): bool
    {
        // Administrator i manager mogą anulować wszystkie inwestycje
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        // Użytkownik może anulować tylko swoje inwestycje w statusie "zainteresowany" lub "w trakcie rozmów"
        return $user->id === $investment->user_id && 
               ($investment->isInterested() || $investment->isInTalks());
    }
    
    /**
     * Określa czy użytkownik może przywrócić usuniętą inwestycję.
     */
    public function restore(User $user, Investment $investment): bool
    {
        // Tylko administrator może przywracać usunięte inwestycje
        return $user->isAdmin();
    }
    
    /**
     * Określa czy użytkownik może trwale usunąć inwestycję.
     */
    public function forceDelete(User $user, Investment $investment): bool
    {
        // Tylko administrator może trwale usuwać inwestycje
        return $user->isAdmin();
    }
    
    /**
     * Określa czy użytkownik może zmienić status inwestycji.
     */
    public function changeStatus(User $user, Investment $investment): bool
    {
        Log::info('InvestmentPolicy@changeStatus - sprawdzanie uprawnień', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'investment_id' => $investment->id,
            'project_id' => $investment->project_id,
            'project_owner_id' => $investment->project->owner_id,
            'is_admin' => $user->isAdmin() ? 'TAK' : 'NIE',
            'is_manager' => $user->isManager() ? 'TAK' : 'NIE',
            'is_project_owner' => ($user->id === $investment->project->owner_id) ? 'TAK' : 'NIE'
        ]);

        // Administrator, manager i właściciel projektu mogą zmieniać status inwestycji
        $result = $user->isAdmin() || 
                $user->isManager() || 
                $user->id === $investment->project->owner_id;
        
        Log::info('InvestmentPolicy@changeStatus - wynik: ' . ($result ? 'TAK' : 'NIE'));
        return $result;
    }
    
    /**
     * Określa czy użytkownik może przeglądać statystyki platformy.
     */
    public function viewStatistics(User $user): bool
    {
        // Administratorzy, managerowie i użytkownicy z planem premium-owner mogą przeglądać statystyki
        return $user->hasAnyRole(['admin', 'manager']) || 
               ($user->hasActiveSubscription() && $user->plan_type === 'premium-owner');
    }
    
    /**
     * Określa czy użytkownik może inwestować w ekskluzywne projekty.
     */
    public function investInExclusive(User $user): bool
    {
        // Tylko użytkownicy z planem premium mogą inwestować w ekskluzywne projekty
        return $user->hasActiveSubscription() && 
               in_array($user->plan_type, ['premium-investor', 'premium-owner']);
    }
}
