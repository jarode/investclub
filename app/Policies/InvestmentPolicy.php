<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvestmentPolicy
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
     * Określa czy użytkownik może przeglądać inwestycje.
     */
    public function viewAny(User $user): bool
    {
        // Każdy zalogowany użytkownik może przeglądać dostępne inwestycje
        return true;
    }
    
    /**
     * Określa czy użytkownik może przeglądać szczegóły konkretnej inwestycji.
     */
    public function view(User $user, $investment): bool
    {
        // Każdy zalogowany użytkownik może przeglądać dostępne inwestycje
        return true;
    }
    
    /**
     * Określa czy użytkownik może tworzyć nowe inwestycje.
     */
    public function create(User $user): bool
    {
        // Tylko administrator i manager mogą tworzyć inwestycje
        return $user->hasAnyRole(['admin', 'manager']);
    }
    
    /**
     * Określa czy użytkownik może aktualizować inwestycję.
     */
    public function update(User $user, $investment): bool
    {
        // Tylko administrator i manager mogą aktualizować inwestycje
        return $user->hasAnyRole(['admin', 'manager']);
    }
    
    /**
     * Określa czy użytkownik może usuwać inwestycję.
     */
    public function delete(User $user, $investment): bool
    {
        // Tylko administrator może usuwać inwestycje
        return $user->isAdmin();
    }
    
    /**
     * Określa czy użytkownik może inwestować.
     */
    public function invest(User $user, $investment): bool
    {
        // Użytkownik może inwestować, jeśli jest zweryfikowany
        return $user->isVerified();
    }
    
    /**
     * Określa czy użytkownik może przeglądać raporty finansowe inwestycji.
     */
    public function viewReports(User $user, $investment): bool
    {
        // Tylko administrator, manager i księgowy mogą przeglądać raporty
        return $user->hasAnyRole(['admin', 'manager', 'accountant']);
    }
}
