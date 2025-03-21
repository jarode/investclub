<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'verification_status',
        'kyc_status',
        'stripe_id',
        'stripe_subscription_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is an administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'Administrator';
    }

    /**
     * Check if user is a manager.
     *
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->role === 'manager' || $this->role === 'Manager';
    }

    /**
     * Check if user is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
    
    /**
     * Check if user has completed KYC verification.
     *
     * @return bool
     */
    public function isKycVerified(): bool
    {
        return $this->kyc_status === 'verified';
    }
    
    /**
     * Accessor for kyc_verified attribute.
     *
     * @return bool
     */
    public function getKycVerifiedAttribute(): bool
    {
        return $this->isKycVerified();
    }
    
    /**
     * Sprawdza czy użytkownik ma aktywną subskrypcję.
     *
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        return $this->stripe_subscription_status === 'active';
    }
    
    /**
     * Accessor dla statusu subskrypcji.
     *
     * @return bool
     */
    public function getActiveSubscriptionAttribute(): bool
    {
        return $this->hasActiveSubscription();
    }
    
    /**
     * Sprawdza czy użytkownik może zarządzać projektami.
     *
     * @return bool
     */
    public function canManageProjects(): bool
    {
        // Użytkownik może zarządzać projektami, jeśli ma plan O-Premium
        // lub rolę administratora/managera
        return $this->isAdmin() || 
               $this->isManager() || 
               ($this->hasActiveSubscription() && $this->plan_type === 'premium-owner');
    }
    
    /**
     * Sprawdza czy użytkownik ma pełny dostęp do systemu.
     *
     * @return bool
     */
    public function hasFullAccess(): bool
    {
        // Pełny dostęp mają administratorzy lub użytkownicy z planem O-Premium
        return $this->isAdmin() || 
              ($this->hasActiveSubscription() && $this->plan_type === 'premium-owner');
    }
    
    /**
     * Sprawdza czy użytkownik ma określoną rolę.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        // Dla roli 'project_owner' lub 'manager' sprawdzamy subskrypcję O-Premium
        if (strtolower($role) === 'project_owner') {
            return $this->canManageProjects();
        }
        
        // Obsługuje zarówno nazwy ról z dużych liter (Administrator) jak i małych (admin)
        $normalisedRole = strtolower($this->role);
        $normalisedRoleToCheck = strtolower($role);
        return $normalisedRole === $normalisedRoleToCheck;
    }
    
    /**
     * Sprawdza czy użytkownik ma jedną z wielu określonych ról.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        $normalisedRole = strtolower($this->role);
        return in_array($normalisedRole, array_map('strtolower', $roles));
    }
    
    /**
     * Pobiera projekty utworzone przez użytkownika.
     */
    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }
    
    /**
     * Pobiera inwestycje użytkownika.
     */
    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}
