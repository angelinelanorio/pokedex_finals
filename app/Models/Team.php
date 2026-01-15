<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id', 'pokemon_id', 'nickname', 'level', 
        'experience', 'is_active', 'date_caught', 'caught_location'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_caught' => 'date',
        'level' => 'integer',
        'experience' => 'integer'
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
    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?: $this->pokemon->name;
    }

    public function getExperienceForNextLevelAttribute(): int
    {
        if ($this->level == 1) {
            return 50; 
        }
        return 50 + (($this->level - 1) * 100);
    }

    // Add method para sa current level experience needed
    public function getCurrentLevelExperienceNeeded(): int
    {
        return $this->experience_for_next_level;
    }

    public function getExperienceProgressAttribute(): float
    {
        $expNeeded = $this->experience_for_next_level;
        if ($expNeeded == 0) {
            return 0;
        }
        return min(100, ($this->experience / $expNeeded) * 100);
    }

    public function addExperience(int $exp): void
{
    \Log::info("=== ADD EXPERIENCE METHOD ===");
    \Log::info("Before: Level {$this->level}, Exp: {$this->experience}");
    \Log::info("Adding: {$exp} EXP");
    
    $this->experience += $exp;
    
    // Check if can level up
    while ($this->experience >= $this->experience_for_next_level && $this->level < 100) {
        \Log::info("CAN LEVEL UP!");
        \Log::info("Exp needed: {$this->experience_for_next_level}");
        \Log::info("Current exp: {$this->experience}");
        
        // Subtract the exp needed for current level
        $this->experience -= $this->experience_for_next_level;
        $this->level++;
        
        \Log::info("Leveled up to: {$this->level}");
        \Log::info("Remaining exp: {$this->experience}");
    }
    
    $this->save();
    
    \Log::info("After: Level {$this->level}, Exp: {$this->experience}");
    \Log::info("Exp needed for next level: {$this->experience_for_next_level}");
}
    
    // Check if can level up
    private function canLevelUp(): bool
    {
        $expNeeded = $this->experience_for_next_level;
        return $this->experience >= $expNeeded && $this->level < 100;
    }
    
    // Level up logic
    private function levelUp(): void
    {
        $expNeeded = $this->experience_for_next_level;
        
        \Log::info("Level up from level {$this->level}");
        \Log::info("Exp needed: {$expNeeded}, Current exp: {$this->experience}");
        
        // Subtract needed exp
        $this->experience -= $expNeeded;
        
        // Level up
        $this->level++;
        
        \Log::info("Leveled up to {$this->level}, Remaining exp: {$this->experience}");
        
        // Ensure experience doesn't go negative
        if ($this->experience < 0) {
            $this->experience = 0;
        }
    }
}