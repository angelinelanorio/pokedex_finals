<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Team;
use App\Models\CatchHistory;
use Illuminate\Http\Request;

class CatchController extends Controller
{
    // Show catch interface - UPDATE ITO
    public function index()
    {
        // Check if user is logged in via session
        $trainerId = session('trainer_id');
        
        $randomPokemon = Pokemon::inRandomOrder()->first();
        
        $catchStats = $this->getCatchStatsPublic(); // BAGONG FUNCTION
        $recentCatches = $this->getRecentCatchesPublic(); // BAGONG FUNCTION
        
        return view('pokemon.index', compact('randomPokemon', 'catchStats', 'recentCatches'));
    }
    
    // Attempt to catch a Pokémon - UPDATE ITO
    public function attemptCatch(Request $request)
    {
        // Check if user is logged in
        if (!session('logged_in')) {
            return response()->json([
                'success' => false,
                'error' => 'Please login first!'
            ]);
        }
        
        $request->validate([
            'pokemon_id' => 'required|exists:pokemons,id'
        ]);
        
        $pokemonId = $request->pokemon_id;
        $trainerId = session('trainer_id'); // GAMITIN ANG SESSION
        $pokemon = Pokemon::find($pokemonId);
        
        // Check if already caught
        if (Team::where('trainer_id', $trainerId)->where('pokemon_id', $pokemonId)->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Pokémon already caught!'
            ]);
        }
        
        // Calculate catch chance
        $catchChance = $this->calculateCatchChance($pokemon);
        $success = rand(0, 100) < $catchChance;
        
        // Record attempt
        CatchHistory::create([
            'trainer_id' => $trainerId, // SESSION ID
            'pokemon_id' => $pokemonId,
            'success' => $success,
            'location' => $request->location ?? 'Wild',
            'method' => $request->method ?? 'pokeball'
        ]);
        
        if ($success) {
            // Add to collection
            Team::create([
                'trainer_id' => $trainerId, // SESSION ID
                'pokemon_id' => $pokemonId,
                'level' => 5,
                'experience' => 0,
                'is_active' => true,
                'date_caught' => now(),
                'caught_location' => $request->location ?? 'Wild'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Congratulations! You caught ' . $pokemon->name . '!',
                'pokemon' => $pokemon,
                'catch_chance' => $catchChance
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Oh no! ' . $pokemon->name . ' escaped!',
                'catch_chance' => $catchChance
            ]);
        }
    }
    
    // ADD THESE PUBLIC FUNCTIONS:
    public function getCatchStatsPublic()
    {
        $trainerId = session('trainer_id');
        
        if (!$trainerId) {
            return [
                'total_catches' => 0,
                'success_rate' => 0,
                'total_attempts' => 0,
                'successful_catches' => 0,
                'failed_catches' => 0,
                'today_catches' => 0
            ];
        }
        
        $totalAttempts = CatchHistory::where('trainer_id', $trainerId)->count();
        $successfulCatches = CatchHistory::where('trainer_id', $trainerId)
                                        ->where('success', true)
                                        ->count();
        $failedCatches = $totalAttempts - $successfulCatches;
        $successRate = $totalAttempts > 0 ? round(($successfulCatches / $totalAttempts) * 100, 2) : 0;
        
        // Today's catches
        $todayCatches = CatchHistory::where('trainer_id', $trainerId)
                                   ->whereDate('created_at', today())
                                   ->where('success', true)
                                   ->count();
        
        // Total caught from Team table
        $totalCaught = Team::where('trainer_id', $trainerId)->count();
        
        return [
            'total_catches' => $totalCaught,
            'success_rate' => $successRate,
            'total_attempts' => $totalAttempts,
            'successful_catches' => $successfulCatches,
            'failed_catches' => $failedCatches,
            'today_catches' => $todayCatches
        ];
    }
    
    public function getRecentCatchesPublic()
    {
        $trainerId = session('trainer_id');
        
        if (!$trainerId) {
            return collect(); // Return empty collection
        }
        
        return CatchHistory::where('trainer_id', $trainerId)
                          ->with('pokemon')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
    }
    
    // Get random Pokémon for catching
    public function getRandomPokemon()
    {
        $pokemon = Pokemon::inRandomOrder()->first();
        
        if (!$pokemon) {
            return response()->json([
                'success' => false,
                'error' => 'No Pokémon available'
            ]);
        }
        
        // Calculate catch chance
        $catchChance = $this->calculateCatchChance($pokemon);
        
        return response()->json([
            'success' => true,
            'pokemon' => $pokemon,
            'catch_chance' => $catchChance
        ]);
    }
    
    // Get catch statistics
    public function getStats()
    {
        $trainerId = session('trainer_id');
        
        if (!$trainerId) {
            return response()->json([
                'success' => true,
                'stats' => [
                    'total_attempts' => 0,
                    'successful_catches' => 0,
                    'failed_catches' => 0,
                    'success_rate' => 0,
                    'today_catches' => 0
                ]
            ]);
        }
        
        $totalAttempts = CatchHistory::where('trainer_id', $trainerId)->count();
        $successfulCatches = CatchHistory::where('trainer_id', $trainerId)
                                        ->where('success', true)
                                        ->count();
        $failedCatches = $totalAttempts - $successfulCatches;
        $successRate = $totalAttempts > 0 ? round(($successfulCatches / $totalAttempts) * 100, 2) : 0;
        
        // Today's catches
        $todayCatches = CatchHistory::where('trainer_id', $trainerId)
                                   ->whereDate('created_at', today())
                                   ->where('success', true)
                                   ->count();
        
        return response()->json([
            'success' => true,
            'stats' => [
                'total_attempts' => $totalAttempts,
                'successful_catches' => $successfulCatches,
                'failed_catches' => $failedCatches,
                'success_rate' => $successRate,
                'today_catches' => $todayCatches
            ]
        ]);
    }
    
    // Calculate catch chance (keep as private)
    private function calculateCatchChance($pokemon)
    {
        // Base catch rate (40%)
        $catchRate = 40;
        
        // Adjust based on HP
        $catchRate *= (100 / ($pokemon->hp + 50));
        
        // Adjust for rare types
        if (in_array($pokemon->type1, ['dragon', 'psychic', 'ghost']) || 
            in_array($pokemon->type2, ['dragon', 'psychic', 'ghost'])) {
            $catchRate *= 0.7;
        }
        
        // Ensure between 5% and 80%
        $catchRate = max(5, min(80, $catchRate));
        
        return round($catchRate, 2);
    }
}