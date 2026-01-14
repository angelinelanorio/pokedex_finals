<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer1_id', 'trainer2_id', 'pokemon1_id', 'pokemon2_id',
        'status', 'notes'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    protected $with = ['trainer1', 'trainer2', 'pokemon1', 'pokemon2'];

    // Relationships
    public function trainer1(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer1_id');
    }

    public function trainer2(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer2_id');
    }

    public function pokemon1(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class, 'pokemon1_id');
    }

    public function pokemon2(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class, 'pokemon2_id');
    }

    // Accessors
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getTradeDateAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i');
    }

    // Methods
    public function completeTrade(): bool
    {
        $this->status = 'completed';
        
        // Update trainer stats
        $this->trainer1->increment('trades_completed');
        $this->trainer2->increment('trades_completed');
        
        return $this->save();
    }

    public function rejectTrade(): bool
    {
        $this->status = 'rejected';
        return $this->save();
    }

    // Check if trainer is involved in trade
    public function involvesTrainer(int $trainerId): bool
    {
        return $this->trainer1_id === $trainerId || $this->trainer2_id === $trainerId;
    }

    // Get the other trainer in trade
    public function getOtherTrainer(int $trainerId): ?Trainer
    {
        if ($this->trainer1_id === $trainerId) {
            return $this->trainer2;
        } elseif ($this->trainer2_id === $trainerId) {
            return $this->trainer1;
        }
        
        return null;
    }

    // Get the Pokémon offered by a trainer
    public function getPokemonOfferedByTrainer(int $trainerId): ?Pokemon
    {
        if ($this->trainer1_id === $trainerId) {
            return $this->pokemon1;
        } elseif ($this->trainer2_id === $trainerId) {
            return $this->pokemon2;
        }
        
        return null;
    }

    // Get the Pokémon received by a trainer
    public function getPokemonReceivedByTrainer(int $trainerId): ?Pokemon
    {
        if ($this->trainer1_id === $trainerId) {
            return $this->pokemon2;
        } elseif ($this->trainer2_id === $trainerId) {
            return $this->pokemon1;
        }
        
        return null;
    }
}