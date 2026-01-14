<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AITrainer extends Model
{
    use HasFactory;

    protected $table = 'ai_trainers';

    protected $fillable = [
        'name', 'region', 'level', 'description', 'avatar_color',
        'pokemon_count', 'trades_count', 'trade_preferences', 'personality',
        'team_data'
    ];

    protected $casts = [
        'trade_preferences' => 'array',
        'personality' => 'array',
        'team_data' => 'array',
        'level' => 'integer',
        'pokemon_count' => 'integer',
        'trades_count' => 'integer'
    ];

    // Accessors
    public function getTeamAttribute(): array
    {
        return $this->team_data ?: [];
    }

    public function getTradePreferencesAttribute($value): array
    {
        $default = [
            'wants' => ['normal', 'fire', 'water'],
            'offers' => ['grass', 'electric'],
            'fairness' => 'medium',
            'minLevel' => 5,
            'maxLevelDifference' => 10,
            'preferredStats' => ['attack', 'speed'],
            'avoidTypes' => [],
            'tradeStyle' => 'balanced'
        ];

        return array_merge($default, json_decode($value, true) ?: []);
    }

    public function getPersonalityAttribute($value): array
    {
        $default = [
            'patience' => 5,
            'generosity' => 5,
            'stubbornness' => 5
        ];

        return array_merge($default, json_decode($value, true) ?: []);
    }

    // Check if AI trainer would accept trade
    public function wouldAcceptTrade(array $playerPokemon, array $aiPokemon): array
    {
        $playerValue = $this->calculateTradeValue($playerPokemon);
        $aiValue = $this->calculateTradeValue($aiPokemon);
        
        $valueRatio = $playerValue / $aiValue;
        $prefs = $this->trade_preferences;
        
        // Check fairness based on preferences
        $tolerance = 0.2 + ($this->personality['generosity'] * 0.05);
        
        if ($valueRatio < (1 - $tolerance) && $prefs['fairness'] !== 'lenient') {
            return ['acceptable' => false, 'reason' => 'Trade not fair enough for me'];
        }
        
        if ($valueRatio > (1 + $tolerance) && $prefs['fairness'] === 'strict') {
            return ['acceptable' => false, 'reason' => 'This trade is too generous'];
        }
        
        return ['acceptable' => true, 'reason' => 'I accept this trade!'];
    }

    private function calculateTradeValue(array $pokemon): int
    {
        // Simple value calculation
        $value = ($pokemon['level'] ?? 5) * 10;
        
        // Add type bonuses
        $typeValues = [
            'dragon' => 30, 'psychic' => 25, 'legendary' => 50,
            'fire' => 20, 'water' => 20, 'electric' => 20,
            'normal' => 10
        ];
        
        foreach ($pokemon['types'] ?? [] as $type) {
            $value += $typeValues[$type] ?? 5;
        }
        
        return $value;
    }
}