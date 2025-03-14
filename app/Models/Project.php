<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes;
    
    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'target_amount',
        'current_amount',
        'min_investment',
        'status',
        'start_date',
        'end_date',
        'returns_projection',
        'risk_level',
        'owner_id',
    ];
    
    /**
     * Atrybuty, które powinny być rzutowane.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'min_investment' => 'decimal:2',
        'returns_projection' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    /**
     * Relacja do użytkownika będącego właścicielem projektu.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    /**
     * Relacja do inwestycji związanych z projektem.
     */
    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
    
    /**
     * Sprawdza, czy projekt jest aktywny.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    
    /**
     * Sprawdza, czy projekt został w pełni sfinansowany.
     */
    public function isFunded(): bool
    {
        return $this->status === 'funded' || $this->current_amount >= $this->target_amount;
    }
    
    /**
     * Sprawdza, czy projekt został zakończony.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    
    /**
     * Oblicza procent finansowania projektu.
     */
    public function fundingPercentage(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        
        $percentage = ($this->current_amount / $this->target_amount) * 100;
        return round($percentage, 2);
    }
    
    /**
     * Oblicza pozostałą kwotę do osiągnięcia celu.
     */
    public function remainingAmount(): float
    {
        $remaining = $this->target_amount - $this->current_amount;
        return max(0, $remaining);
    }
    
    /**
     * Sprawdza, czy użytkownik jest właścicielem projektu.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }
}
