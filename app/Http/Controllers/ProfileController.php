<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show trainer profile
    public function show()
{
    $trainerId = session('trainer_id');
    
    if (!$trainerId) {
        return redirect()->route('login')->with('error', 'Please login first');
    }
    
    $trainer = Trainer::find($trainerId);
    
    if (!$trainer) {
        session()->flush();
        return redirect()->route('login')->with('error', 'Trainer not found');
    }
    
    // Get recent activity
    $recentActivity = $this->getRecentActivity($trainer);
    
    // Get badges earned
    $badges = $this->getBadges($trainer);
    
    // Get trading partners
    $tradingPartners = $this->getTradingPartners($trainer);
    
    // CALCULATE TEAM COUNT AND TRADE COUNT 
    $teamCount = $trainer->activeTeam()->count();
    
    // Calculate trades count properly
    $tradeCount = \App\Models\Trade::where(function($query) use ($trainer) {
        $query->where('trainer1_id', $trainer->id)
              ->orWhere('trainer2_id', $trainer->id);
    })->count();
    
    // Update trainer's stats if they don't match
    if ($trainer->pokemon_caught != $teamCount) {
        $trainer->update(['pokemon_caught' => $teamCount]);
    }
    
    if ($trainer->trades_completed != $tradeCount) {
        $trainer->update(['trades_completed' => $tradeCount]);
    }
    
    // Refresh trainer data
    $trainer->refresh();
    
    return view('profile.show', compact(
        'trainer', 
        'recentActivity', 
        'badges', 
        'tradingPartners',
        'teamCount',  
        'tradeCount' 
    ));
}
    
    // Update profile
    public function update(Request $request)
    {
        $trainerId = session('trainer_id');
    
    if (!$trainerId) {
        return redirect()->route('login');
    }
    
    $trainer = Trainer::find($trainerId);
        
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'region' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:500',
            'avatar_url' => 'nullable|url',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed'
        ]);
        
        // Update basic info
        $trainer->update([
            'first_name' => $validated['first_name'] ?? $trainer->first_name,
            'last_name' => $validated['last_name'] ?? $trainer->last_name,
            'region' => $validated['region'] ?? $trainer->region,
            'bio' => $validated['bio'] ?? $trainer->bio,
            'avatar_url' => $validated['avatar_url'] ?? $trainer->avatar_url
        ]);
        
        // Update password if provided
        if ($request->has('new_password')) {
            if (!Hash::check($request->current_password, $trainer->password)) {
                return redirect()->back()
                               ->with('error', 'Current password is incorrect!');
            }
            
            $trainer->update([
                'password' => Hash::make($request->new_password)
            ]);
        }
        
        return redirect()->route('profile.show')
                        ->with('success', 'Profile updated successfully!');
    }
    
    // Get recent activity
    private function getRecentActivity($trainer)
{
    $activities = collect();
    
    // Recent catches 
    $recentCatches = $trainer->teams()
                            ->with('pokemon')
                            ->orderBy('date_caught', 'desc')
                            ->limit(5)
                            ->get()
                            ->map(function($team) {
                                // Check if pokemon exists
                                if (!$team->pokemon) {
                                    return null;
                                }
                                return [
                                    'type' => 'catch',
                                    'message' => "Caught {$team->pokemon->name}",
                                    'time' => $team->date_caught->diffForHumans(),
                                    'icon' => 'fas fa-pokeball'
                                ];
                            })
                            ->filter(); 
    
    $activities = $activities->merge($recentCatches);
    
    // Recent trades 
    try {
        $recentTrades = \App\Models\Trade::where(function($query) use ($trainer) {
            $query->where('trainer1_id', $trainer->id)
                  ->orWhere('trainer2_id', $trainer->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function($trade) use ($trainer) {
            // Determine other trainer
            $otherTrainerId = $trade->trainer1_id == $trainer->id ? $trade->trainer2_id : $trade->trainer1_id;
            
            // Get other trainer's name
            $otherTrainerName = 'Unknown';
            
            if ($otherTrainerId >= 100) {
                // AI Trainer - get from AITrainer model
                $aiTrainer = \App\Models\AITrainer::find($otherTrainerId);
                if ($aiTrainer) {
                    $otherTrainerName = $aiTrainer->name;
                }
            } else {
                // Real Trainer - get from Trainer model
                $realTrainer = \App\Models\Trainer::find($otherTrainerId);
                if ($realTrainer) {
                    $otherTrainerName = $realTrainer->username;
                }
            }
            
            // Get Pokémon names
            $givenPokemonName = 'Unknown Pokémon';
            $receivedPokemonName = 'Unknown Pokémon';
            
            // Get given Pokémon
            if ($trade->trainer1_id == $trainer->id) {
                $givenPokemon = \App\Models\Pokemon::find($trade->pokemon1_id);
                if ($givenPokemon) {
                    $givenPokemonName = $givenPokemon->name;
                }
            } else {
                $givenPokemon = \App\Models\Pokemon::find($trade->pokemon2_id);
                if ($givenPokemon) {
                    $givenPokemonName = $givenPokemon->name;
                }
            }
            
            // Get received Pokémon
            if ($trade->trainer1_id == $trainer->id) {
                $receivedPokemon = \App\Models\Pokemon::find($trade->pokemon2_id);
                if ($receivedPokemon) {
                    $receivedPokemonName = $receivedPokemon->name;
                }
            } else {
                $receivedPokemon = \App\Models\Pokemon::find($trade->pokemon1_id);
                if ($receivedPokemon) {
                    $receivedPokemonName = $receivedPokemon->name;
                }
            }
            
            return [
                'type' => 'trade',
                'message' => "Traded {$givenPokemonName} for {$receivedPokemonName} with {$otherTrainerName}",
                'time' => $trade->created_at->diffForHumans(),
                'icon' => 'fas fa-exchange-alt'
            ];
        })
        ->filter(); 
        
        $activities = $activities->merge($recentTrades);
    } catch (\Exception $e) {
        \Log::error('Error getting trade history: ' . $e->getMessage());
    }
    
    // Sort by time
    return $activities->sortByDesc('time')->values()->take(10);
}

    // Get badges earned
    private function getBadges($trainer)
    {
        $badges = [];
        
        // Pokédex completion badges
        $pokedexProgress = $trainer->pokedex_progress;
        
        if ($pokedexProgress['percentage'] >= 25) {
            $badges[] = [
                'name' => 'Pokédex Bronze',
                'description' => 'Caught 25% of all Pokémon',
                'icon' => 'fas fa-medal',
                'color' => '#cd7f32'
            ];
        }
        
        if ($pokedexProgress['percentage'] >= 50) {
            $badges[] = [
                'name' => 'Pokédex Silver',
                'description' => 'Caught 50% of all Pokémon',
                'icon' => 'fas fa-medal',
                'color' => '#c0c0c0'
            ];
        }
        
        if ($pokedexProgress['percentage'] >= 75) {
            $badges[] = [
                'name' => 'Pokédex Gold',
                'description' => 'Caught 75% of all Pokémon',
                'icon' => 'fas fa-medal',
                'color' => '#ffd700'
            ];
        }
        
        if ($pokedexProgress['percentage'] >= 100) {
            $badges[] = [
                'name' => 'Pokédex Master',
                'description' => 'Caught all Pokémon',
                'icon' => 'fas fa-trophy',
                'color' => '#ff6b6b'
            ];
        }
        
        // Trading badges
        if ($trainer->trades_completed >= 5) {
            $badges[] = [
                'name' => 'Trader',
                'description' => 'Completed 5 trades',
                'icon' => 'fas fa-exchange-alt',
                'color' => '#4cc9f0'
            ];
        }
        
        if ($trainer->trades_completed >= 20) {
            $badges[] = [
                'name' => 'Master Trader',
                'description' => 'Completed 20 trades',
                'icon' => 'fas fa-handshake',
                'color' => '#7209b7'
            ];
        }
        
        // Team badges
        if ($trainer->activeTeam()->count() == 6) {
            $badges[] = [
                'name' => 'Team Complete',
                'description' => 'Has a full team of 6 Pokémon',
                'icon' => 'fas fa-users',
                'color' => '#2a9d8f'
            ];
        }
        
        return $badges;
    }
    
    // Get trading partners
    private function getTradingPartners($trainer)
{
    // Method 1: Using raw query with manual loading
    $partners = \App\Models\Trade::where(function($query) use ($trainer) {
        $query->where('trainer1_id', $trainer->id)
              ->orWhere('trainer2_id', $trainer->id);
    })
    ->selectRaw('CASE WHEN trainer1_id = ? THEN trainer2_id ELSE trainer1_id END as partner_id', [$trainer->id])
    ->selectRaw('COUNT(*) as trade_count')
    ->groupBy('partner_id')
    ->orderBy('trade_count', 'desc')
    ->limit(5)
    ->get()
    ->map(function($item) {
        $partner = \App\Models\Trainer::find($item->partner_id);
        
        if (!$partner) {
            return null;
        }
        
        return [
            'trainer' => $partner,
            'trade_count' => $item->trade_count
        ];
    })
    ->filter(); 
    
    return $partners;
}
    
    // Get trainer statistics
    public function getStats()
    {
        $trainer = Auth::user();
        
        $stats = [
            'pokemon_caught' => $trainer->pokemon_caught,
            'trades_completed' => $trainer->trades_completed,
            'badges_earned' => $trainer->badges_earned,
            'team_size' => $trainer->activeTeam()->count(),
            'pokedex_percentage' => $trainer->pokedex_progress['percentage'],
            'catch_success_rate' => $this->calculateCatchSuccessRate($trainer)
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
    
    // Calculate catch success rate
    private function calculateCatchSuccessRate($trainer)
    {
        $totalAttempts = \App\Models\CatchHistory::where('trainer_id', $trainer->id)->count();
        $successfulCatches = \App\Models\CatchHistory::where('trainer_id', $trainer->id)
                                                    ->where('success', true)
                                                    ->count();
        
        if ($totalAttempts == 0) {
            return 0;
        }
        
        return round(($successfulCatches / $totalAttempts) * 100, 2);
    }
}