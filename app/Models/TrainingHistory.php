<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'team_id',
        'pokemon_id',
        'exp_gained',
        'training_type',
        'old_level',
        'new_level',
        'old_experience',
        'new_experience',
        'leveled_up'
    ];

    protected $casts = [
        'leveled_up' => 'boolean',
        'exp_gained' => 'integer',
        'old_level' => 'integer',
        'new_level' => 'integer',
        'old_experience' => 'integer',
        'new_experience' => 'integer'
    ];

    // Relationships
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }

    // Check if Pokémon has reached daily training limit
    public static function hasReachedDailyLimit($teamId): bool
    {
        $today = now()->startOfDay();
        
        $count = self::where('team_id', $teamId)
            ->where('created_at', '>=', $today)
            ->count();
            
        return $count >= 3;
    }

    // Get today's trainings for a Pokémon
    public static function getTodayTrainings($teamId)
    {
        $today = now()->startOfDay();
        
        return self::where('team_id', $teamId)
            ->where('created_at', '>=', $today)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Get remaining daily trainings
    public static function getRemainingTrainings($teamId): int
    {
        $count = self::where('team_id', $teamId)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
            
        return max(0, 3 - $count);
    }
}