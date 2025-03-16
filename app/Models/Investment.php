<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * Atrybuty, które można masowo przypisywać.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'amount',
        'status',
        'contact_preference',
        'contact_details',
        'notes',
    ];
    
    /**
     * Atrybuty, które powinny być rzutowane na typy.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Możliwe statusy inwestycji.
     */
    const STATUS_INTERESTED = 'interested';
    const STATUS_IN_TALKS = 'in_talks';
    const STATUS_CONTRACT_SIGNED = 'contract_signed';
    const STATUS_CANCELLED = 'cancelled';
    
    /**
     * Relacja do użytkownika (inwestora).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relacja do projektu.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Sprawdza, czy inwestor jest zainteresowany.
     */
    public function isInterested(): bool
    {
        return $this->status === self::STATUS_INTERESTED;
    }
    
    /**
     * Sprawdza, czy trwają rozmowy.
     */
    public function isInTalks(): bool
    {
        return $this->status === self::STATUS_IN_TALKS;
    }
    
    /**
     * Sprawdza, czy umowa została podpisana.
     */
    public function isContractSigned(): bool
    {
        return $this->status === self::STATUS_CONTRACT_SIGNED;
    }
    
    /**
     * Sprawdza, czy inwestycja jest w statusie "anulowana".
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
    
    /**
     * Sprawdza, czy inwestycja jest aktywna (nie anulowana).
     */
    public function isActive(): bool
    {
        return !$this->isCancelled();
    }
    
    /**
     * Oblicza przewidywaną wartość zwrotu inwestycji.
     */
    public function calculateReturnValue(): float
    {
        return $this->amount * (1 + ($this->project->returns_projection / 100));
    }
    
    /**
     * Oblicza potencjalny zysk z inwestycji.
     */
    public function calculateProfit(): float
    {
        return $this->calculateReturnValue() - $this->amount;
    }
    
    /**
     * Zwraca listę możliwych statusów inwestycji.
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_INTERESTED => 'Zainteresowany',
            self::STATUS_IN_TALKS => 'W trakcie rozmów',
            self::STATUS_CONTRACT_SIGNED => 'Umowa podpisana',
            self::STATUS_CANCELLED => 'Anulowana',
        ];
    }
}
