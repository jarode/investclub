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
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_subscription_status',
        'plan_type',
        'is_verified'
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean'
    ];

    /**
     * Check if user is an administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Administrator' || $this->role === 'admin';
    }

    /**
     * Check if user is a manager.
     *
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is an investor.
     *
     * @return bool
     */
    public function isInvestor(): bool
    {
        return $this->role === 'investor';
    }

    /**
     * Check if user is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }
    
    /**
     * Sprawdza czy użytkownik ma aktywną subskrypcję.
     *
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        // Sprawdzamy czy używamy nowego pola subscription_status czy starszego stripe_subscription_status
        if (isset($this->attributes['subscription_status'])) {
            return $this->subscription_status === 'active';
        }
        
        if (isset($this->attributes['stripe_subscription_status'])) {
            return $this->stripe_subscription_status === 'active';
        }
        
        return false;
    }
    
    /**
     * Sprawdza czy subskrypcja jest w trakcie anulowania.
     *
     * @return bool
     */
    public function hasSubscriptionPendingCancellation(): bool
    {
        return $this->stripe_subscription_status === 'active' && $this->cancellation_requested;
    }
    
    /**
     * Sprawdza czy użytkownik ma określony typ planu.
     *
     * @param string $planType
     * @return bool
     */
    public function hasPlanType(string $planType): bool
    {
        // Sprawdzamy czy używamy nowego pola subscription_type czy starszego plan_type
        if (isset($this->attributes['subscription_type'])) {
            return $this->subscription_type === $planType;
        }
        
        if (isset($this->attributes['plan_type'])) {
            return $this->plan_type === $planType;
        }
        
        return false;
    }
    
    /**
     * Sprawdza czy użytkownik ma jeden z określonych typów planów.
     *
     * @param array $planTypes
     * @return bool
     */
    public function hasAnyPlanType(array $planTypes): bool
    {
        // Sprawdzamy czy używamy nowego pola subscription_type czy starszego plan_type
        if (isset($this->attributes['subscription_type'])) {
            return in_array($this->subscription_type, $planTypes);
        }
        
        if (isset($this->attributes['plan_type'])) {
            return in_array($this->plan_type, $planTypes);
        }
        
        return false;
    }
    
    /**
     * Sprawdza czy użytkownik ma darmowy plan.
     *
     * @return bool
     */
    public function hasFreePlan(): bool
    {
        if (isset($this->attributes['subscription_type'])) {
            return $this->hasActiveSubscription() && $this->subscription_type === 'free-investor';
        }
        
        return $this->hasActiveSubscription() && $this->plan_type === 'free-investor';
    }
    
    /**
     * Sprawdza czy użytkownik jest właścicielem projektu.
     *
     * @return bool
     */
    public function isProjectOwner(): bool
    {
        if (isset($this->attributes['subscription_type'])) {
            return $this->hasActiveSubscription() && $this->subscription_type === 'premium-owner';
        }
        
        return $this->hasActiveSubscription() && $this->plan_type === 'premium-owner';
    }
    
    /**
     * Sprawdza czy użytkownik jest inwestorem premium.
     *
     * @return bool
     */
    public function isPremiumInvestor(): bool
    {
        if (isset($this->attributes['subscription_type'])) {
            return $this->hasActiveSubscription() && $this->subscription_type === 'premium-investor';
        }
        
        return $this->hasActiveSubscription() && $this->plan_type === 'premium-investor';
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

    /**
     * Sprawdza czy użytkownik ma uprawnienia do zarządzania projektami.
     *
     * @return bool
     */
    public function canManageProjects(): bool
    {
        // Dla administratora zawsze true
        if ($this->role === 'Administrator' || $this->role === 'admin') {
            return true;
        }
        
        // Sprawdzamy czy używamy nowego pola subscription_type czy starszego plan_type
        if (isset($this->attributes['subscription_type'])) {
            return $this->subscription_type === 'premium-owner';
        }
        
        return $this->plan_type === 'premium-owner';
    }

    /**
     * Sprawdza czy użytkownik ma określoną rolę.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Sprawdza czy użytkownik ma jedną z podanych ról.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
}
