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
        'wallet_balance',
        'kyc_status',
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
            'wallet_balance' => 'decimal:2',
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
     * Check if user is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }
    
    /**
     * Sprawdza czy użytkownik ma określoną rolę.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
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
