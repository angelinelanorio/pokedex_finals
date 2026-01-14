<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatchHistory extends Model
{
    use HasFactory;

    // ADD THIS LINE - SPECIFY TABLE NAME
    protected $table = 'catch_history';
    
    protected $fillable = [
        'trainer_id', 'pokemon_id', 'success', 'location', 'method'
    ];

    protected $casts = [
        'success' => 'boolean'
    ];

    protected $with = ['pokemon'];

    // Relationships
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }

    // Accessors
    public function getCatchDateAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i');
    }

    public function getStatusTextAttribute(): string
    {
        return $this->success ? 'Successful' : 'Failed';
    }

    public function getStatusClassAttribute(): string
    {
        return $this->success ? 'success' : 'failed';
    }

    // Methods
    public function markAsSuccessful(): bool
    {
        $this->success = true;
        return $this->save();
    }

    public function markAsFailed(): bool
    {
        $this->success = false;
        return $this->save();
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeByTrainer($query, int $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    public function scopeByPokemon($query, int $pokemonId)
    {
        return $query->where('pokemon_id', $pokemonId);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}