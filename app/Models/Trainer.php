<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Trainer extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'region',
        'level',
        'avatar_url',
        'bio',
        'pokemon_caught',
        'trades_completed',
        'badges_earned',
        'daily_streak',
        'last_login_date',
        'total_logins'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'pokemon_caught' => 'integer',
        'trades_completed' => 'integer',
        'badges_earned' => 'integer',
        'level' => 'integer',
        'last_login_date' => 'date'
    ];

    // ============ SIMPLE DAILY LOGIN SYSTEM ============
    
    /**
     * Process daily login - SUPER SIMPLE VERSION
     * EVERY LOGIN = +1 LEVEL
     */
    public function processDailyLogin()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // Check if already logged in today
        if ($this->last_login_date && $this->last_login_date->isToday()) {
            return [
                'message' => 'Already logged in today!',
                'level_up' => false,
                'streak' => $this->daily_streak,
                'total_logins' => $this->total_logins
            ];
        }
        
        // Update streak counter
        if ($this->last_login_date && $this->last_login_date->isYesterday()) {
            // Consecutive login - increase streak
            $this->daily_streak++;
        } else {
            // Missed a day or first login - reset to 1
            $this->daily_streak = 1;
        }
        
        // ============ IMPORTANT: +1 LEVEL EVERY LOGIN! ============
        $oldLevel = $this->level;
        $this->level++; // THIS IS THE MAGIC LINE!
        
        // Update counters
        $this->last_login_date = $today;
        $this->total_logins++;
        
        $this->save();
        
        // Simple message
        $messages = [
            "Welcome back, {$this->first_name}!",
            "Level up! You're now Level {$this->level}!",
            "Daily streak: {$this->daily_streak} days!",
            "Keep coming back for more levels!"
        ];
        
        return [
            'level_up' => true,
            'old_level' => $oldLevel,
            'new_level' => $this->level,
            'streak' => $this->daily_streak,
            'total_logins' => $this->total_logins,
            'message' => $messages[array_rand($messages)]
        ];
    }
    
    /**
     * Get streak info (simple version)
     */
    public function getStreakInfo()
    {
        $today = Carbon::today();
        
        return [
            'current_streak' => $this->daily_streak,
            'total_logins' => $this->total_logins,
            'last_login' => $this->last_login_date ? $this->last_login_date->format('M d, Y') : 'Never',
            'today_logged_in' => $this->last_login_date && $this->last_login_date->isToday(),
            'level' => $this->level
        ];
    }
    // Relationships
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function activeTeam()
    {
        return $this->hasMany(Team::class)->where('is_active', true);
    }

    // Relationships for trading
    public function tradesAsTrainer1()
    {
        return $this->hasMany(Trade::class, 'trainer1_id');
    }

    public function tradesAsTrainer2()
    {
        return $this->hasMany(Trade::class, 'trainer2_id');
    }

    public function allTrades()
    {
        return Trade::where('trainer1_id', $this->id)
                    ->orWhere('trainer2_id', $this->id);
    }

    public function catchHistory()
    {
        return $this->hasMany(CatchHistory::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->username;
    }

    public function getDisplayNameAttribute()
    {
        return $this->first_name ? $this->first_name : $this->username;
    }

    // Team related accessors
    public function getTeamCountAttribute()
    {
        return $this->activeTeam()->count();
    }

    public function getPokedexProgressAttribute()
    {
        $totalPokemon = Pokemon::count();
        $caughtPokemon = $this->teams()->distinct('pokemon_id')->count();
        
        return [
            'caught' => $caughtPokemon,
            'total' => $totalPokemon,
            'percentage' => $totalPokemon > 0 ? round(($caughtPokemon / $totalPokemon) * 100, 1) : 0
        ];
    }

    // Get all trading partners
    public function tradingPartners()
    {
        $partnerIds1 = $this->tradesAsTrainer1()->pluck('trainer2_id');
        $partnerIds2 = $this->tradesAsTrainer2()->pluck('trainer1_id');
        $allPartnerIds = $partnerIds1->merge($partnerIds2)->unique();
        
        return Trainer::whereIn('id', $allPartnerIds)->get();
    }

    // Calculate badges earned
    public function getBadgesCountAttribute()
    {
        $badges = $this->calculateBadges();
        return count($badges);
    }

    // Calculate badges logic
    private function calculateBadges()
    {
        $badges = [];
        $pokedexProgress = $this->pokedex_progress;
        
        // Pokédex badges
        if ($pokedexProgress['percentage'] >= 25) {
            $badges[] = 'pokedex_bronze';
        }
        if ($pokedexProgress['percentage'] >= 50) {
            $badges[] = 'pokedex_silver';
        }
        if ($pokedexProgress['percentage'] >= 75) {
            $badges[] = 'pokedex_gold';
        }
        if ($pokedexProgress['percentage'] >= 100) {
            $badges[] = 'pokedex_master';
        }
        
        // Trading badges
        if ($this->trades_completed >= 5) {
            $badges[] = 'trader';
        }
        if ($this->trades_completed >= 20) {
            $badges[] = 'master_trader';
        }
        
        // Team badges
        if ($this->team_count >= 6) {
            $badges[] = 'team_complete';
        }
        
        // Level badges
        if ($this->level >= 10) {
            $badges[] = 'experienced_trainer';
        }
        if ($this->level >= 25) {
            $badges[] = 'veteran_trainer';
        }
        if ($this->level >= 50) {
            $badges[] = 'elite_trainer';
        }
        
        return $badges;
    }

    public function getTotalCatchesAttribute()
    {
        return $this->teams()->count();
    }

    public function getTotalTradesAttribute()
    {
        return $this->allTrades()->count();
    }

    public function getActiveTeamCountAttribute()
    {
        return $this->activeTeam()->count();
    }
}