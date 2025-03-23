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
        return $this->role === 'admin';
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
        return $this->stripe_subscription_status === 'active';
    }
    
    /**
     * Sprawdza czy użytkownik jest właścicielem projektu.
     *
     * @return bool
     */
    public function isProjectOwner(): bool
    {
        return $this->hasActiveSubscription() && $this->plan_type === 'premium-owner';
    }
    
    /**
     * Sprawdza czy użytkownik jest inwestorem premium.
     *
     * @return bool
     */
    public function isPremiumInvestor(): bool
    {
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
        return $this->plan_type === 'premium-owner' ||
               $this->role === 'admin';
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
