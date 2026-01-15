<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\CatchHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PokemonController extends Controller
{

public function index(Request $request)
{
    // Get total Pokemon count
    $totalPokemonCount = Pokemon::count();
    
    // GET CAUGHT COUNT FROM CATCH_HISTORY TABLE
    $caughtCount = 0;
    
    if (session('logged_in') && session('trainer_id')) {
        $caughtCount = CatchHistory::byTrainer(session('trainer_id'))
            ->successful()
            ->count();
            
        // Debug logging
        \Log::info("PokemonController - Trainer ID: " . session('trainer_id'));
        \Log::info("PokemonController - Caught Count: " . $caughtCount);
        
        // Also log the actual query results
        $catches = CatchHistory::where('trainer_id', session('trainer_id'))->get();
        \Log::info("All catches for trainer: " . $catches->toJson());
    }
    
    // Check active tab
    $activeTab = $request->get('tab', 'list');
    
    if ($activeTab === 'catch') {
        // CREATE CATCHCONTROLLER INSTANCE
        $catchController = new CatchController();
        
        // Get random Pokémon
        $randomPokemon = Pokemon::inRandomOrder()->first();
        
        // Get stats from CatchController
        $catchStats = $catchController->getCatchStatsPublic();
        $recentCatches = $catchController->getRecentCatchesPublic();
        
        // Use the stats
        $totalCatches = $catchStats['total_catches'] ?? 0;
        $successRate = $catchStats['success_rate'] ?? 0;
        $rareFinds = 0; 
        
        return view('pokemon.index', [
            'activeTab' => 'catch',
            'randomPokemon' => $randomPokemon,
            'totalCatches' => $totalCatches,
            'successRate' => $successRate,
            'rareFinds' => $rareFinds,
            'recentCatches' => $recentCatches,
            'totalPokemonCount' => $totalPokemonCount,
            'catchStats' => $catchStats,
            'caughtCount' => $caughtCount
        ]);
    }
    
    // DEFAULT: POKEMON LIST TAB
    $query = Pokemon::query();
    
    // Search functionality
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('type1', 'LIKE', "%{$search}%")
              ->orWhere('type2', 'LIKE', "%{$search}%")
              ->orWhere('pokedex_number', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }
    
    // Filter by type
    if ($request->has('type') && $request->type != '') {
        $type = $request->type;
        $query->where(function($q) use ($type) {
            $q->where('type1', $type)
              ->orWhere('type2', $type);
        });
    }
    
    // Order by Pokédex number
    $query->orderBy('pokedex_number');
    
    $pokemons = $query->paginate(20);
    
    return view('pokemon.index', [
        'pokemons' => $pokemons,
        'activeTab' => 'list',
        'totalPokemonCount' => $totalPokemonCount,
        'caughtCount' => $caughtCount
    ]);
}
    
    // Show single Pokémon details
    public function show($id)
    {
        $pokemon = Pokemon::findOrFail($id);
        
        // Mark if caught
        if (session('logged_in')) {
            // $pokemon->caught = Team::where('trainer_id', session('trainer_id'))->where('pokemon_id', $pokemon->id)->exists();
            $pokemon->caught = false; // temporary
        }
        
        // Get evolution info
        $evolvesFrom = null;
        $evolvesTo = null;
        
        if ($pokemon->evolves_from) {
            $evolvesFrom = Pokemon::find($pokemon->evolves_from);
        }
        
        if ($pokemon->evolves_to) {
            $evolvesTo = Pokemon::find($pokemon->evolves_to);
        }
        
        return view('pokemon.index', compact('pokemon', 'evolvesFrom', 'evolvesTo'));
    }
    
    // Show create form
    public function create()
    {
        return view('pokemon.create');
    }
    
    // Store new Pokémon
public function store(Request $request)
{
    // Check if user is logged in
    if (!session('logged_in')) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Please login first'
            ], 401);
        }
        return redirect()->route('login');
    }
    
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type1' => 'required|string|in:normal,fire,water,grass,electric,ice,fighting,poison,ground,flying,psychic,bug,rock,ghost,dragon',
            'type2' => 'nullable|string|in:normal,fire,water,grass,electric,ice,fighting,poison,ground,flying,psychic,bug,rock,ghost,dragon',
            'description' => 'required|string|max:500',
            'height' => 'required|numeric|min:0.1|max:999.99',
            'weight' => 'required|numeric|min:0.1|max:9999.99',
            'image_url' => 'nullable|url',
            'hp' => 'required|integer|min:1|max:255',
            'attack' => 'required|integer|min:1|max:255',
            'defense' => 'required|integer|min:1|max:255',
            'special_attack' => 'required|integer|min:1|max:255',
            'special_defense' => 'required|integer|min:1|max:255',
            'speed' => 'required|integer|min:1|max:255',
        ]);
        
        // Get next available Pokédex number
        $lastPokemon = Pokemon::orderBy('pokedex_number', 'desc')->first();
        $nextNumber = $lastPokemon ? $lastPokemon->pokedex_number + 1 : 152;
        
        $pokemon = Pokemon::create([
            'pokedex_number' => $nextNumber,
            'name' => $validated['name'],
            'type1' => $validated['type1'],
            'type2' => $validated['type2'] ?? null,
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'description' => $validated['description'],
            'image_url' => $validated['image_url'] ?? null,
            'abilities' => 'Custom Ability',
            'moves' => 'Tackle, Growl',
            'hp' => $validated['hp'],
            'attack' => $validated['attack'],
            'defense' => $validated['defense'],
            'special_attack' => $validated['special_attack'],
            'special_defense' => $validated['special_defense'],
            'speed' => $validated['speed'],
            'evolution_stage' => 1,
        ]);
        
        // Check if it's an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pokémon created successfully!',
                'pokemon_id' => $pokemon->id,
                'redirect_url' => route('pokemon.index')
            ]);
        }
        
        return redirect()->route('pokemon.index', $pokemon->id)
                        ->with('success', 'Pokémon created successfully!');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        return back()->withErrors($e->errors())->withInput();
        
    } catch (\Exception $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create Pokémon: ' . $e->getMessage()
            ], 500);
        }
        
        return back()->with('error', 'Failed to create Pokémon: ' . $e->getMessage())->withInput();
    }
}
    
    // Show edit form
    public function edit($id)
    {
        $pokemon = Pokemon::findOrFail($id);
        return view('pokemon.edit', compact('pokemon'));
    }
    
    // Update Pokémon 
public function update(Request $request, $id)
{
    error_log("UPDATE CALLED for ID: " . $id);
    error_log("Request data: " . print_r($request->all(), true));
    
    try {
        $pokemon = Pokemon::find($id);
        
        if (!$pokemon) {
            return response()->json([
                'success' => false,
                'error' => 'Pokémon not found'
            ]);
        }
        
        // UPDATE
        $pokemon->name = $request->input('name');
        $pokemon->type1 = $request->input('type1');
        $pokemon->type2 = $request->input('type2');
        $pokemon->description = $request->input('description');
        $pokemon->height = $request->input('height');
        $pokemon->weight = $request->input('weight');
        
        $pokemon->save();
        
        // RETURN JSON PALAGI
        return response()->json([
            'success' => true,
            'message' => 'Pokémon updated successfully!'
        ]);
        
    } catch (\Exception $e) {
        error_log("ERROR: " . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Error: ' . $e->getMessage()
        ]);
    }
}
    
    // Delete Pokémon
    public function destroy($id)
    {
        $pokemon = Pokemon::findOrFail($id);
        $pokemon->delete();
        
        return redirect()->route('pokemon.index')
                        ->with('success', 'Pokémon deleted successfully!');
    }
    
    // API: Get all Pokémon
    public function apiIndex(Request $request)
    {
        $query = Pokemon::query();
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }
        
        if ($request->has('type') && $request->type != '') {
            $query->where('type1', $request->type)
                  ->orWhere('type2', $request->type);
        }
        
        $pokemons = $query->orderBy('pokedex_number')->get();
        
        return response()->json([
            'success' => true,
            'pokemon' => $pokemons
        ]);
    }
    
    // API: Get single Pokémon
    public function apiShow($id)
    {
        $pokemon = Pokemon::find($id);
        
        if (!$pokemon) {
            return response()->json([
                'success' => false,
                'error' => 'Pokémon not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'pokemon' => $pokemon
        ]);
    }
    public function catchGame(Request $request)
{
    // Get random Pokémon
    $randomPokemon = Pokemon::inRandomOrder()->first();
    
    // Get user's catch stats 
    $totalCatches = 0;
    $successRate = 0;
    $rareFinds = 0;
    
    // Get recent catches
    $recentCatches = [];
    
    return view('pokemon.catch', compact('randomPokemon', 'totalCatches', 'successRate', 'rareFinds', 'recentCatches'));
}
}