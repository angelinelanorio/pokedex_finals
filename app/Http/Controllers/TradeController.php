<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Trade;
use App\Models\Team;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TradeController extends Controller
{
    // Show trading interface with history
    public function index(Request $request)
    {
        // Get current user from session
        $userId = session('trainer_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }
        
        $user = Trainer::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Trainer not found!');
        }
        
        // Get current user's active team Pokémon
        $yourPokemon = Team::with(['pokemon'])
    ->where('trainer_id', $userId)
    ->where('is_active', true)
    ->get()
    ->map(function($team) {
        $pokemon = $team->pokemon;
        $pokemon->level = $team->level;
        return $pokemon;
    });
        
        // If no Pokémon found, get some for demo
        if ($yourPokemon->isEmpty()) {
            $yourPokemon = Pokemon::limit(3)->get();
        }
        
        // Get real trainers (excluding current user)
        $realTrainers = Trainer::where('id', '!=', $user->id)
            ->get()
            ->map(function($trainer) {
                // Get team Pokémon for this trainer
                $teamPokemon = Pokemon::join('teams', 'pokemons.id', '=', 'teams.pokemon_id')
                    ->where('teams.trainer_id', $trainer->id)
                    ->where('teams.is_active', true)
                    ->select('pokemons.*', 'teams.level')
                    ->get();
                
                return [
                    'id' => $trainer->id,
                    'username' => $trainer->username,
                    'region' => $trainer->region,
                    'level' => $trainer->level,
                    'avatar_url' => $trainer->avatar_url,
                    'pokemon_caught' => $trainer->pokemon_caught,
                    'trades_completed' => $trainer->trades_completed,
                    'is_ai' => false,
                    'avatar_color' => '#95a5a6',
                    'description' => 'Real trainer from ' . $trainer->region,
                    'team' => $teamPokemon
                ];
            });
        
$aiTrainers = \App\Models\AITrainer::all()
    ->map(function($aiTrainer) {
        $teamPokemon = collect();
        
        // Handle string or array team_data
        $teamData = $aiTrainer->team_data;
        
        // If string, decode it
        if (is_string($teamData)) {
            $teamData = json_decode($teamData, true);
        }
        
        // Check if valid array
        if ($teamData && is_array($teamData)) {
            foreach ($teamData as $teamMember) {
                if (!is_array($teamMember) || !isset($teamMember['pokemon_id'])) {
                    continue;
                }
                
                $pokemon = Pokemon::find($teamMember['pokemon_id']);
                if ($pokemon) {
                    $pokemon->level = $teamMember['level'] ?? $this->getAIPokemonLevel($pokemon->id, $aiTrainer->id);
                    $teamPokemon->push($pokemon);
                }
            }
        }
        
        // Fallback if no valid team data
        if ($teamPokemon->isEmpty()) {
            $fallbackTeam = $this->getAITeam($aiTrainer->id);
            $teamPokemon = $fallbackTeam;
        }
        
        return [
            'id' => $aiTrainer->id,
            'username' => $aiTrainer->name,
            'region' => $aiTrainer->region,
            'level' => $aiTrainer->level,
            'avatar_url' => null,
            'pokemon_caught' => $aiTrainer->pokemon_count,
            'trades_completed' => $aiTrainer->trades_count,
            'is_ai' => true,
            'avatar_color' => $aiTrainer->avatar_color,
            'description' => $aiTrainer->description,
            'team' => $teamPokemon
        ];
    })->toArray();
        
        // Combine all trainers
        $allTrainers = array_merge($realTrainers->toArray(), $aiTrainers);
        
        // Handle selected trainer
        $selectedTrainer = null;
        $selectedTrainerTeam = collect();
        
        if (session('selected_trainer_id')) {
            $trainerId = session('selected_trainer_id');
            
            // Find selected trainer
            foreach ($allTrainers as $trainer) {
                if ($trainer['id'] == $trainerId) {
                    $selectedTrainer = (object) $trainer;
                    $selectedTrainerTeam = collect($trainer['team']);
                    break;
                }
            }
        }
        
        // Get all trades for the user
        $trades = Trade::where(function($query) use ($userId) {
            $query->where('trainer1_id', $userId)
                  ->orWhere('trainer2_id', $userId);
        })
        ->with(['trainer1', 'trainer2', 'pokemon1', 'pokemon2'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        
        // Calculate trade statistics
        $tradeStats = [
            'total_trades' => $trades->total(),
            'completed_trades' => Trade::where(function($query) use ($userId) {
                $query->where('trainer1_id', $userId)
                      ->orWhere('trainer2_id', $userId);
            })->where('status', 'completed')->count(),
            'ai_trades' => Trade::where(function($query) use ($userId) {
                $query->where('trainer1_id', $userId)
                      ->orWhere('trainer2_id', $userId);
            })->where(function($query) {
                $query->where('trainer1_id', '>=', 100)
                      ->orWhere('trainer2_id', '>=', 100);
            })->count()
        ];
        
        // Check if we're showing trade history tab
        $showHistory = $request->has('tab') && $request->tab == 'history';
        
        return view('trading.index', [
            'allTrainers' => $allTrainers,
            'selectedTrainer' => $selectedTrainer,
            'selectedTrainerTeam' => $selectedTrainerTeam,
            'yourPokemon' => $yourPokemon,
            'trades' => $trades,
            'tradeStats' => $tradeStats,
            'showHistory' => $showHistory
        ]);
    }
    
    // Get AI's Pokémon offer
    public function getAIOffer(Request $request)
{
    $request->validate([
        'trainer_id' => 'required',
        'your_pokemon_id' => 'required|exists:pokemons,id'
    ]);
    
    $userId = session('trainer_id');
    $aiTrainerId = $request->trainer_id;
    $yourPokemonId = $request->your_pokemon_id;
    
    if (!$userId) {
        return response()->json([
            'success' => false,
            'error' => 'Please login first!'
        ], 401);
    }
    
    // Check if trainer is AI
    if ($aiTrainerId < 100) {
        return response()->json([
            'success' => false,
            'error' => 'This feature is only for AI trainers'
        ]);
    }
    
    $yourPokemon = Pokemon::findOrFail($yourPokemonId);
    
    // ============ NEW: GET YOUR POKÉMON'S LEVEL ============
    $yourLevel = 1; 
    $yourTeamMember = Team::where('trainer_id', $userId)
                         ->where('pokemon_id', $yourPokemonId)
                         ->where('is_active', true)
                         ->first();
    
    if ($yourTeamMember) {
        $yourLevel = $yourTeamMember->level ?? 1;
    }
    
    $yourValue = $this->calculatePokemonValue($yourPokemon);
    $yourValue *= ($yourLevel / 10); 
    
    // Get AI's team Pokémon
    $aiTeam = $this->getAITeam($aiTrainerId);
    
    if ($aiTeam->isEmpty()) {
        return response()->json([
            'success' => false,
            'error' => 'AI trainer has no Pokémon to trade'
        ]);
    }
    
    // Find fair Pokémon from AI's team 
    $fairOffers = collect();
    foreach ($aiTeam as $pokemon) {
        // Skip same Pokémon
        if ($pokemon->id == $yourPokemonId) {
            continue;
        }
        
        // Get AI Pokémon level
        $aiPokemonLevel = $this->getAIPokemonLevel($pokemon->id, $aiTrainerId);
        
        // Skip if level difference is too big (more than 10 levels)
        $levelDiff = abs($aiPokemonLevel - $yourLevel);
        if ($levelDiff > 10) {
            continue;
        }
        
        // Adjust Pokémon value based on level
        $pokemonValue = $this->calculatePokemonValue($pokemon);
        $pokemonValue *= ($aiPokemonLevel / 10);
        
        $ratio = $pokemonValue / $yourValue;
        
        // Check if trade is fair (within 50% difference for now)
        if ($ratio >= 0.5 && $ratio <= 1.5) {
            $fairOffers->push([
                'pokemon' => $pokemon,
                'value' => $pokemonValue,
                'level' => $aiPokemonLevel,
                'fairness_score' => abs(1 - $ratio) + ($levelDiff / 100)
            ]);
        }
    }
    
    // If no fair offers, try with larger level difference
    if ($fairOffers->isEmpty()) {
        $closest = null;
        $closestDiff = PHP_FLOAT_MAX;
        
        foreach ($aiTeam as $pokemon) {
            if ($pokemon->id == $yourPokemonId) {
                continue;
            }
            
            $aiPokemonLevel = $this->getAIPokemonLevel($pokemon->id, $aiTrainerId);
            $levelDiff = abs($aiPokemonLevel - $yourLevel);
            
            // Still skip if level difference is HUGE (more than 20 levels)
            if ($levelDiff > 20) {
                continue;
            }
            
            $pokemonValue = $this->calculatePokemonValue($pokemon);
            $pokemonValue *= ($aiPokemonLevel / 10);
            
            $diff = abs($pokemonValue - $yourValue) + ($levelDiff * 10);
            
            if ($diff < $closestDiff) {
                $closestDiff = $diff;
                $closest = $pokemon;
                $closestLevel = $aiPokemonLevel;
            }
        }
        
        if ($closest) {
            $fairOffers->push([
                'pokemon' => $closest,
                'value' => $this->calculatePokemonValue($closest) * ($closestLevel / 10),
                'level' => $closestLevel,
                'fairness_score' => $closestDiff / $yourValue
            ]);
        }
    }
    
    // Sort by fairness (closest to 1)
    $fairOffers = $fairOffers->sortBy('fairness_score');
    
    if ($fairOffers->isEmpty()) {
        return response()->json([
            'success' => false,
            'error' => 'Your Pokémon level is too low for trading with this AI trainer. Try leveling up first!'
        ]);
    }
    
    // Get the fairest offer
    $bestOffer = $fairOffers->first();
    $bestOffer['pokemon']->level = $bestOffer['level'];

    return response()->json([
    'success' => true,
    'pokemon' => $bestOffer['pokemon'],
    'value' => $bestOffer['value'],
    'level' => $bestOffer['level'],
    'fairness_score' => $bestOffer['fairness_score'],
    'fairness_message' => $this->getFairnessMessage($bestOffer['fairness_score']) . 
                          ' (Your Level: ' . $yourLevel . ' vs AI Level: ' . $bestOffer['level'] . ')'
]);
}
    
    // Execute a trade
public function store(Request $request)
{
    // Check if user is logged in
    $userId = session('trainer_id');
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please login first!');
    }
    
    $request->validate([
        'trainer_id' => 'required',
        'your_pokemon_id' => 'required|exists:pokemons,id',
        'their_pokemon_id' => 'required|exists:pokemons,id'
    ]);
    
    DB::beginTransaction();
    
    try {
        $trainer1 = Trainer::find($userId);
        if (!$trainer1) {
            throw new \Exception('Trainer not found!');
        }
        
        $trainer2Id = $request->trainer_id;
        $pokemon1Id = $request->your_pokemon_id;
        $pokemon2Id = $request->their_pokemon_id;
        
        $pokemon1 = Pokemon::findOrFail($pokemon1Id);
        $pokemon2 = Pokemon::findOrFail($pokemon2Id);
        
        // Check if you own the Pokémon you're trading
        $yourTeamMember = Team::where('trainer_id', $userId)
                             ->where('pokemon_id', $pokemon1->id)
                             ->where('is_active', true)
                             ->first();
        
        if (!$yourTeamMember) {
            throw new \Exception('You do not own this Pokémon or it\'s not in your active team!');
        }
        
        // Get actual levels for level-based fairness check
$yourLevel = $yourTeamMember->level ?? 1;
$theirLevel = 1;

if ($trainer2Id >= 100) {
    // AI trainer - get AI Pokémon level
    $theirLevel = $this->getAIPokemonLevel($pokemon2->id, $trainer2Id);
} else {
    // Real trainer - get their Pokémon level
    $theirTeam = Team::where('trainer_id', $trainer2Id)
        ->where('pokemon_id', $pokemon2->id)
        ->where('is_active', true)
        ->first();
    $theirLevel = $theirTeam->level ?? 1;
}

// LEVEL-BASED FAIRNESS CHECK
$levelDiff = abs($yourLevel - $theirLevel);

// Allow max 3 levels difference
if ($levelDiff > 3) {
    throw new \Exception('Trade is not fair! Level difference too large. Your Level: ' . $yourLevel . ' vs Their Level: ' . $theirLevel);
}
        
        // Trading with AI trainer
if ($trainer2Id >= 100) {
    // AI trainer
    $aiId = $trainer2Id;
    $aiNames = [
        101 => 'Brock',
        102 => 'Misty', 
        103 => 'Ash',
        104 => 'Gary',
        105 => 'Professor Oak'
    ];
    
    $trainer2DisplayName = $aiNames[$aiId] ?? 'AI Trainer';
    
    // ======== GET AI POKÉMON ACTUAL LEVEL ========
    $aiPokemonLevel = $this->getAIPokemonLevel($pokemon2->id, $trainer2Id);
    
    // ======== DEACTIVATE YOUR POKÉMON ========
    $yourTeamMember->update([
        'is_active' => false,
        'caught_location' => 'Traded to ' . $trainer2DisplayName,
        'updated_at' => now()
    ]);
    
    // ======== CHECK IF YOU ALREADY HAVE IT ========
    $existingPokemon = Team::where('trainer_id', $trainer1->id)
        ->where('pokemon_id', $pokemon2->id)
        ->where('is_active', true)
        ->first();
    
    if ($existingPokemon) {
        $newLevel = max($existingPokemon->level, $aiPokemonLevel);
        $existingPokemon->update([
            'level' => $newLevel,
            'updated_at' => now()
        ]);
        
        $successMessage = 'Trade completed! ' . $pokemon2->name . 
                         ' updated to level ' . $newLevel . '!';
    } else {
        Team::updateOrCreate(
            [
                'trainer_id' => $trainer1->id,
                'pokemon_id' => $pokemon2->id
            ],
            [
                'level' => $aiPokemonLevel, 
                'experience' => 0,
                'is_active' => true,
                'date_caught' => now(),
                'caught_location' => 'Trade with ' . $trainer2DisplayName,
                'nickname' => $pokemon2->name,
                'updated_at' => now()
            ]
        );
        
        // Increment caught count if NEW
        if (!Team::where('trainer_id', $trainer1->id)
                 ->where('pokemon_id', $pokemon2->id)
                 ->where('is_active', true)
                 ->exists()) {
            $trainer1->increment('pokemon_caught');
        }
        
        $successMessage = 'Trade completed successfully with ' . $trainer2DisplayName . 
                         '! You received ' . $pokemon2->name . ' (Level ' . $aiPokemonLevel . ')!';
    }

        } else {
            // Real trainer
            $trainer2 = Trainer::findOrFail($trainer2Id);
            
            // Check if other trainer has the Pokémon in their active team
            $theirTeamMember = Team::where('trainer_id', $trainer2->id)
                                  ->where('pokemon_id', $pokemon2->id)
                                  ->where('is_active', true)
                                  ->first();
            
            if (!$theirTeamMember) {
                throw new \Exception('The other trainer does not have this Pokémon in their active team!');
            }
            
            // Swap trainers
            $yourTeamMember->update([
                'trainer_id' => $trainer2->id,
                'caught_location' => 'Traded from ' . $trainer1->username,
                'updated_at' => now()
            ]);
            
            $theirTeamMember->update([
                'trainer_id' => $trainer1->id,
                'caught_location' => 'Traded from ' . $trainer2->username,
                'updated_at' => now()
            ]);
            
            // Increment trade counts
            $trainer2->increment('trades_completed');
            $trainer2DisplayName = $trainer2->username;
            
            $successMessage = 'Trade completed successfully!';
        }
        
        // Increment current trainer's trade count
        $trainer1->increment('trades_completed');
        
        // Record the trade
        if ($trainer2Id < 100) { 
    // Real trainer
    $trainer2 = Trainer::find($trainer2Id);
    if ($trainer2) {
        $trainer2DisplayName = $trainer2->username;
    } else {
        $trainer2DisplayName = 'Unknown Trainer';
    }
    
    Trade::create([
        'trainer1_id' => $trainer1->id,
        'trainer2_id' => $trainer2Id,
        'pokemon1_id' => $pokemon1->id,
        'pokemon2_id' => $pokemon2->id,
        'status' => 'completed',
        'notes' => 'Direct trade with ' . $trainer2DisplayName
    ]);
} else {
    // AI trainer - get AI name
    $aiTrainer = \App\Models\AITrainer::find($trainer2Id);
    $aiName = $aiTrainer ? $aiTrainer->name : 'AI Trainer';
    
    DB::table('trades')->insert([
        'trainer1_id' => $trainer1->id,
        'trainer2_id' => $trainer2Id,
        'pokemon1_id' => $pokemon1->id,
        'pokemon2_id' => $pokemon2->id,
        'status' => 'completed',
        'notes' => 'Trade with AI Trainer: ' . $aiName,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}
        
        DB::commit();
        
        return redirect()->route('trading.index')
                        ->with('success', $successMessage);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
                       ->with('error', $e->getMessage());
    }
}
    
    // Check if trade is fair
private function isTradeFair(Pokemon $pokemon1, Pokemon $pokemon2, $level1 = 1, $level2 = 1): bool
{ 
    // Kung same level or +/- 3 levels, FAIR
    $levelDiff = abs($level1 - $level2);
    
    // Allow difference of 3 levels (1↔1, 1↔2, 1↔3, 1↔4)
    return $levelDiff <= 3;
}
    
    // Calculate Pokémon trade value
    private function calculatePokemonValue(Pokemon $pokemon): float
    {
        $value = 0;
        
        // Base stats value
        $value += $pokemon->hp * 0.5;
        $value += $pokemon->attack * 0.8;
        $value += $pokemon->defense * 0.7;
        $value += $pokemon->speed * 0.6;
        $value += $pokemon->special_attack * 0.7;
        $value += $pokemon->special_defense * 0.7;
        
        // Type value multipliers
        $typeValues = [
            'dragon' => 1.5, 'psychic' => 1.3, 'ghost' => 1.3,
            'fire' => 1.2, 'water' => 1.2, 'electric' => 1.2,
            'grass' => 1.1, 'ice' => 1.1, 'fighting' => 1.1,
            'flying' => 1.1, 'ground' => 1.1, 'rock' => 1.05,
            'bug' => 1.0, 'poison' => 1.0, 'normal' => 0.9,
            'fairy' => 1.2, 'steel' => 1.1, 'dark' => 1.2
        ];
        
        $type1Multiplier = $typeValues[$pokemon->type1] ?? 1.0;
        $value *= $type1Multiplier;
        
        if ($pokemon->type2) {
            $type2Multiplier = $typeValues[$pokemon->type2] ?? 1.0;
            $value *= $type2Multiplier;
        }
        
        // Rarity bonus based on Pokédex number
        if ($pokemon->pokedex_number <= 10) {
            $value *= 1.5;
        } elseif ($pokemon->pokedex_number <= 151) {
            $value *= 1.2;
        }
        
        return $value;
    }
    
    // Select trainer for trading
    public function selectTrainer(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required'
        ]);
        
        session(['selected_trainer_id' => $request->trainer_id]);
        
        return redirect()->route('trading.index')
                        ->with('success', 'Trainer selected!');
    }
    
    // Clear selected trainer
    public function clearSelection()
    {
        session()->forget('selected_trainer_id');
        return redirect()->route('trading.index')
                        ->with('info', 'Trainer selection cleared');
    }
    
    // Get AI's team Pokémon
private function getAITeam($aiTrainerId)
{
    $aiTrainer = \App\Models\AITrainer::find($aiTrainerId);
    
    if ($aiTrainer) {
        $teamData = $aiTrainer->team_data;
        
        // If string, decode it
        if (is_string($teamData)) {
            $teamData = json_decode($teamData, true);
        }
        
        if ($teamData && is_array($teamData)) {
            $team = collect();
            
            foreach ($teamData as $teamMember) {
                if (!is_array($teamMember) || !isset($teamMember['pokemon_id'])) {
                    continue; 
                }
                
                $pokemon = Pokemon::find($teamMember['pokemon_id']);
                if ($pokemon) {
                    $pokemon->level = $teamMember['level'] ?? $this->getAIPokemonLevel($pokemon->id, $aiTrainerId);
                    $team->push($pokemon);
                }
            }
            
            if ($team->isNotEmpty()) {
                return $team;
            }
        }
    }
    
    // Fallback to your Pokémon IDs from seeder
    $aiData = [
        101 => ['name' => 'Brock', 'team_ids' => [15, 18, 19]], // Pikachu, Bulbasaur, Charmander
        102 => ['name' => 'Misty', 'team_ids' => [20, 15, 21]], // Squirtle, Pikachu, Gengar
        103 => ['name' => 'Ash', 'team_ids' => [15, 18, 19]],   // Pikachu, Bulbasaur, Charmander
        104 => ['name' => 'Gary', 'team_ids' => [15, 18, 19]],  // Pikachu, Bulbasaur, Charmander
        105 => ['name' => 'Professor Oak', 'team_ids' => [20, 21, 15]] // Squirtle, Gengar, Pikachu
    ];
    
    if (!isset($aiData[$aiTrainerId])) {
        return collect();
    }
    
    return Pokemon::whereIn('id', $aiData[$aiTrainerId]['team_ids'])
        ->get()
        ->map(function($pokemon) use ($aiTrainerId) {
            $pokemon->level = $this->getAIPokemonLevel($pokemon->id, $aiTrainerId);
            return $pokemon;
        });
}
    
    // Get fairness message
    private function getFairnessMessage($fairnessScore)
    {
        if ($fairnessScore <= 0.1) {
            return 'Perfectly fair trade!';
        } elseif ($fairnessScore <= 0.2) {
            return 'Very fair trade!';
        } elseif ($fairnessScore <= 0.3) {
            return 'Fair trade!';
        } else {
            return 'Somewhat fair trade';
        }
    }

    private function getAIPokemonLevel($pokemonId, $aiTrainerId)
{
    $aiTrainer = \App\Models\AITrainer::find($aiTrainerId);
    
    if ($aiTrainer) {
        $teamData = $aiTrainer->team_data;
        
        // If string, decode it
        if (is_string($teamData)) {
            $teamData = json_decode($teamData, true);
        }
        
        if ($teamData && is_array($teamData)) {
            foreach ($teamData as $teamMember) {
                if (isset($teamMember['pokemon_id']) && $teamMember['pokemon_id'] == $pokemonId) {
                    return $teamMember['level'] ?? 20;
                }
            }
        }
    }
    
    // 🔵 PHASE 2: Fallback to predefined array BASED ON SEEDER
    $predefinedLevels = [
        101 => [15 => 35, 18 => 33, 19 => 32], // Brock's levels
        102 => [20 => 28, 15 => 30, 21 => 25], // Misty's levels
        103 => [15 => 18, 18 => 16, 19 => 14], // Ash's levels
        104 => [15 => 12, 18 => 10, 19 => 8],  // Gary's levels
        105 => [20 => 22, 21 => 20, 15 => 25]  // Oak's levels
    ];
    
    if (isset($predefinedLevels[$aiTrainerId][$pokemonId])) {
        return $predefinedLevels[$aiTrainerId][$pokemonId];
    }
    
    // 🔵 PHASE 3: Default level based on trainer
    $trainerDefaultLevels = [
        101 => 35, // Brock
        102 => 28, // Misty
        103 => 18, // Ash
        104 => 12, // Gary
        105 => 22  // Oak
    ];
    
    return $trainerDefaultLevels[$aiTrainerId] ?? 20;
}
    
    // ==================== Helper methods for AI trainers ====================
    
    /**
     * Create dummy Pokémon for AI trainers when database doesn't have the Pokémon
     */
    private function createDummyPokemonForAI(string $trainerName)
{
    // Since your seeder uses IDs 15, 18, 19, 20, 21
    $pokemonList = [];
    
    switch ($trainerName) {
        case 'Brock':
            $pokemonList = [
                ['id' => 15, 'name' => 'Pikachu', 'type1' => 'electric', 'level' => 35],
                ['id' => 18, 'name' => 'Bulbasaur', 'type1' => 'grass', 'level' => 33],
                ['id' => 19, 'name' => 'Charmander', 'type1' => 'fire', 'level' => 32]
            ];
            break;
        case 'Misty':
            $pokemonList = [
                ['id' => 20, 'name' => 'Squirtle', 'type1' => 'water', 'level' => 28],
                ['id' => 15, 'name' => 'Pikachu', 'type1' => 'electric', 'level' => 30],
                ['id' => 21, 'name' => 'Gengar', 'type1' => 'ghost', 'level' => 25]
            ];
            break;
        case 'Ash':
            $pokemonList = [
                ['id' => 15, 'name' => 'Pikachu', 'type1' => 'electric', 'level' => 18],
                ['id' => 18, 'name' => 'Bulbasaur', 'type1' => 'grass', 'level' => 16],
                ['id' => 19, 'name' => 'Charmander', 'type1' => 'fire', 'level' => 14]
            ];
            break;
        case 'Gary':
            $pokemonList = [
                ['id' => 15, 'name' => 'Pikachu', 'type1' => 'electric', 'level' => 12],
                ['id' => 18, 'name' => 'Bulbasaur', 'type1' => 'grass', 'level' => 10],
                ['id' => 19, 'name' => 'Charmander', 'type1' => 'fire', 'level' => 8]
            ];
            break;
        case 'Professor Oak':
        default:
            $pokemonList = [
                ['id' => 20, 'name' => 'Squirtle', 'type1' => 'water', 'level' => 22],
                ['id' => 21, 'name' => 'Gengar', 'type1' => 'ghost', 'level' => 20],
                ['id' => 15, 'name' => 'Pikachu', 'type1' => 'electric', 'level' => 25]
            ];
            break;
    }
        $collection = collect();
        foreach ($pokemonList as $pokeData) {
            $collection->push((object) [
                'id' => $pokeData['id'],
                'name' => $pokeData['name'],
                'type1' => $pokeData['type1'],
                'type2' => $pokeData['type2'],
                'hp' => $pokeData['hp'],
                'attack' => $pokeData['attack'],
                'defense' => $pokeData['defense'],
                'speed' => $pokeData['speed'],
                'pokedex_number' => $pokeData['id'],
                'image_path' => null,
                'image_url' => null,
                'special_attack' => 50,
                'special_defense' => 50,
                'total_stats' => $pokeData['hp'] + $pokeData['attack'] + $pokeData['defense'] + $pokeData['speed'] + 100
            ]);
        }
        
        return $collection;
    }
    
    private function createDummyAITrainer(array $aiData)
    {
        $team = $this->createDummyPokemonForAI($aiData['name']);
        
        return [
            'id' => $aiData['id'],
            'username' => $aiData['name'],
            'region' => $aiData['region'],
            'level' => $aiData['level'],
            'avatar_url' => null,
            'pokemon_caught' => $aiData['pokemon_count'],
            'trades_completed' => $aiData['trades_count'],
            'is_ai' => true,
            'avatar_color' => $aiData['avatar_color'],
            'description' => $aiData['description'],
            'team' => $team
        ];
    }
}