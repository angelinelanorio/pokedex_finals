<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Trainer;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    // API: Get all Pokémon
    public function getPokemon(Request $request)
    {
        $query = Pokemon::query();
        
        if ($request->has('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('type1', 'LIKE', "%{$request->search}%")
                  ->orWhere('type2', 'LIKE', "%{$request->search}%");
        }
        
        if ($request->has('type')) {
            $query->where(function($q) use ($request) {
                $q->where('type1', $request->type)
                  ->orWhere('type2', $request->type);
            });
        }
        
        if ($request->has('min_hp')) {
            $query->where('hp', '>=', $request->min_hp);
        }
        
        if ($request->has('max_hp')) {
            $query->where('hp', '<=', $request->max_hp);
        }
        
        $pokemon = $query->orderBy('pokedex_number')->get();
        
        // Add caught status if user is authenticated
        if (Auth::check()) {
            $caughtIds = Auth::user()->teams()->pluck('pokemon_id')->toArray();
            foreach ($pokemon as $p) {
                $p->caught = in_array($p->id, $caughtIds);
            }
        }
        
        return response()->json([
            'success' => true,
            'count' => $pokemon->count(),
            'pokemon' => $pokemon
        ]);
    }
    
    // API: Get single Pokémon
    public function getPokemonById($id)
    {
        $pokemon = Pokemon::find($id);
        
        if (!$pokemon) {
            return response()->json([
                'success' => false,
                'error' => 'Pokémon not found'
            ], 404);
        }
        
        // Add caught status
        if (Auth::check()) {
            $pokemon->caught = Auth::user()->teams()->where('pokemon_id', $pokemon->id)->exists();
        }
        
        return response()->json([
            'success' => true,
            'pokemon' => $pokemon
        ]);
    }
    
    // API: Add new Pokémon
    public function addPokemon(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'Authentication required'
            ], 401);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'types' => 'required|array|min:1',
            'description' => 'required|string',
            'height' => 'numeric|min:0.1',
            'weight' => 'numeric|min:0.1',
            'image_url' => 'nullable|url',
            'stats.hp' => 'integer|min:1|max:255',
            'stats.attack' => 'integer|min:1|max:255',
            'stats.defense' => 'integer|min:1|max:255',
            'stats.special_attack' => 'integer|min:1|max:255',
            'stats.special_defense' => 'integer|min:1|max:255',
            'stats.speed' => 'integer|min:1|max:255'
        ]);
        
        // Get next Pokédex number
        $lastPokemon = Pokemon::orderBy('pokedex_number', 'desc')->first();
        $nextNumber = $lastPokemon ? $lastPokemon->pokedex_number + 1 : 152;
        
        $pokemon = Pokemon::create([
            'pokedex_number' => $nextNumber,
            'name' => $validated['name'],
            'type1' => $validated['types'][0],
            'type2' => $validated['types'][1] ?? null,
            'height' => $validated['height'] ?? 0.7,
            'weight' => $validated['weight'] ?? 6.9,
            'description' => $validated['description'],
            'image_url' => $validated['image_url'] ?? null,
            'hp' => $validated['stats']['hp'] ?? 50,
            'attack' => $validated['stats']['attack'] ?? 50,
            'defense' => $validated['stats']['defense'] ?? 50,
            'special_attack' => $validated['stats']['special_attack'] ?? 65,
            'special_defense' => $validated['stats']['special_defense'] ?? 65,
            'speed' => $validated['stats']['speed'] ?? 45,
            'abilities' => 'Custom Ability',
            'moves' => 'Tackle, Growl'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pokémon created successfully!',
            'pokemon_id' => $pokemon->id,
            'pokedex_number' => $pokemon->pokedex_number
        ]);
    }
    
    // API: Get trainers
    public function getTrainers(Request $request)
    {
        $query = Trainer::query();
        
        if ($request->has('search')) {
            $query->where('username', 'LIKE', "%{$request->search}%")
                  ->orWhere('region', 'LIKE', "%{$request->search}%");
        }
        
        if ($request->has('region')) {
            $query->where('region', $request->region);
        }
        
        $trainers = $query->withCount(['teams as pokemon_count'])
                         ->orderBy('username')
                         ->get();
        
        return response()->json([
            'success' => true,
            'count' => $trainers->count(),
            'trainers' => $trainers
        ]);
    }
    
    // API: Add trainer
    public function addTrainer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'region' => 'required|string|max:50',
            'level' => 'integer|min:1|max:100',
            'description' => 'nullable|string',
            'avatar_color' => 'nullable|string'
        ]);
        
        $trainer = Trainer::create([
            'username' => $validated['name'],
            'email' => strtolower(str_replace(' ', '', $validated['name'])) . '@pokedex.com',
            'password' => bcrypt('password123'), // Default password
            'region' => $validated['region'],
            'level' => $validated['level'] ?? 5,
            'bio' => $validated['description'] ?? "A Pokémon trainer from {$validated['region']} region."
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Trainer added successfully!',
            'trainer_id' => $trainer->id
        ]);
    }
    
    // API: Record trade
    public function addTrade(Request $request)
    {
        $validated = $request->validate([
            'trainer1_id' => 'required|exists:trainers,id',
            'trainer2_id' => 'required|exists:trainers,id',
            'pokemon1_id' => 'required|exists:pokemons,id',
            'pokemon2_id' => 'required|exists:pokemons,id',
            'status' => 'nullable|string'
        ]);
        
        $trade = Trade::create([
            'trainer1_id' => $validated['trainer1_id'],
            'trainer2_id' => $validated['trainer2_id'],
            'pokemon1_id' => $validated['pokemon1_id'],
            'pokemon2_id' => $validated['pokemon2_id'],
            'status' => $validated['status'] ?? 'completed'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Trade recorded successfully!',
            'trade_id' => $trade->id
        ]);
    }
    
    // API: Catch Pokémon
    public function catchPokemon(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'Authentication required'
            ], 401);
        }
        
        $validated = $request->validate([
            'pokemon_id' => 'required|exists:pokemons,id'
        ]);
        
        $pokemonId = $validated['pokemon_id'];
        $trainer = Auth::user();
        
        // Check if already caught
        if ($trainer->teams()->where('pokemon_id', $pokemonId)->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Pokémon already caught!'
            ]);
        }
        
        // Calculate catch chance
        $pokemon = Pokemon::find($pokemonId);
        $catchChance = $this->calculateCatchChance($pokemon);
        $success = rand(0, 100) < $catchChance;
        
        if ($success) {
            // Add to collection
            \App\Models\Team::create([
                'trainer_id' => $trainer->id,
                'pokemon_id' => $pokemonId,
                'level' => 5,
                'experience' => 0,
                'is_active' => false,
                'date_caught' => now(),
                'caught_location' => 'Wild Encounter'
            ]);
            
            // Update caught count
            $trainer->increment('pokemon_caught');
            
            return response()->json([
                'success' => true,
                'message' => 'Pokémon caught successfully!',
                'pokemon' => $pokemon
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Pokémon escaped! Try again.'
            ]);
        }
    }
    
    // Calculate catch chance
    private function calculateCatchChance($pokemon)
    {
        $catchRate = 40;
        $catchRate *= (100 / ($pokemon->hp + 50));
        
        if (in_array($pokemon->type1, ['dragon', 'psychic', 'ghost']) || 
            in_array($pokemon->type2, ['dragon', 'psychic', 'ghost'])) {
            $catchRate *= 0.7;
        }
        
        return max(5, min(80, $catchRate));
    }
    
    // API: Get Pokédex progress
    public function getPokedexProgress()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'error' => 'Authentication required'
            ], 401);
        }
        
        $trainer = Auth::user();
        $progress = $trainer->pokedex_progress;
        
        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }
}