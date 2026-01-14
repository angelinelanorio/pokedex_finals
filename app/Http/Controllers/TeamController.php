<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Team;
use App\Models\Trainer;
use App\Models\TrainingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        // Check if user is logged in
        if (!session('logged_in') || !session('trainer_id')) {
            $team = collect();
            $teamStats = [
                'count' => 0,
                'total_power' => 0,
                'avg_level' => 0,
                'types_covered' => 0
            ];
            
            return view('team.index', compact('team', 'teamStats'))
                ->with('demo_mode', true)
                ->with('warning', 'Please login to manage your team.');
        }
        
        // Get trainer ID from session
        $trainerId = session('trainer_id');
        
        // Get all team members for this trainer
        $team = Team::where('trainer_id', $trainerId)
        ->where('is_active', true)
            ->with('pokemon')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // If team is empty
        if ($team->isEmpty()) {
            $teamStats = [
                'count' => 0,
                'total_power' => 0,
                'avg_level' => 0,
                'types_covered' => 0
            ];
            
            return view('team.index', compact('team', 'teamStats'));
        }
        
        // Calculate team stats
        $teamStats = [
            'count' => $team->count(),
            'total_power' => $team->sum(function($member) {
                return $member->pokemon->total_stats ?? 0;
            }),
            'avg_level' => round($team->avg('level') ?? 0, 1),
            'types_covered' => $team->flatMap(function($member) {
                $types = [];
                if ($member->pokemon->type1) $types[] = $member->pokemon->type1;
                if ($member->pokemon->type2) $types[] = $member->pokemon->type2;
                return $types;
            })->unique()->count()
        ];
        
        return view('team.index', compact('team', 'teamStats'));
    }
    
    // DEMO VERSION: Para makita mo agad ang design
    public function demo()
    {
        // Create demo team data
        $team = collect();
        
        // Sample Pokémon data
        $pokemons = Pokemon::take(3)->get();
        
        foreach ($pokemons as $pokemon) {
            $team->push((object)[
                'id' => $pokemon->id,
                'pokemon' => $pokemon,
                'level' => rand(10, 50),
                'experience' => rand(0, 1000),
                'display_name' => $pokemon->name,
                'experience_for_next_level' => 1000,
                'experience_progress' => rand(10, 90)
            ]);
        }
        
        $teamStats = [
            'count' => $team->count(),
            'total_power' => $team->sum(function($member) {
                return $member->pokemon->total_stats ?? 0;
            }),
            'avg_level' => round($team->avg('level') ?? 0, 1),
            'types_covered' => $team->flatMap(function($member) {
                $types = [];
                if ($member->pokemon->type1) $types[] = $member->pokemon->type1;
                if ($member->pokemon->type2) $types[] = $member->pokemon->type2;
                return $types;
            })->unique()->count()
        ];
        
        return view('team.index', compact('team', 'teamStats'))
            ->with('demo_mode', true);
    }
    
    // Add Pokémon to team
    public function store(Request $request)
    {
        if (!session('logged_in') || !session('trainer_id')) {
            return redirect()->route('login')->with('error', 'Please login to add Pokémon.');
        }
        
        $request->validate([
            'pokemon_id' => 'required|exists:pokemons,id'
        ]);
        
        $trainerId = session('trainer_id');
        $pokemonId = $request->pokemon_id;
        
        // Check if team is full (max 6)
        if (Team::where('trainer_id', $trainerId)->count() >= 6) {
            return redirect()->back()
                           ->with('error', 'Your team is full! Maximum 6 Pokémon.');
        }
        
        // Check if already in team
        if (Team::where('trainer_id', $trainerId)
                ->where('pokemon_id', $pokemonId)
                ->exists()) {
            return redirect()->back()
                           ->with('error', 'This Pokémon is already in your team!');
        }
        
        // Add to team
        Team::create([
            'trainer_id' => $trainerId,
            'pokemon_id' => $pokemonId,
            'level' => 5,
            'experience' => 0,
            'is_active' => true,
            'date_caught' => now(),
            'caught_location' => 'Team Addition'
        ]);
        
        // Update caught count
        $trainer = Trainer::find($trainerId);
        if ($trainer) {
            $trainer->increment('pokemon_caught');
        }
        
        return redirect()->route('team.index')
                        ->with('success', 'Pokémon added to your team!');
    }
    
    // Remove Pokémon from team
    public function destroy($id)
    {
        if (!session('logged_in') || !session('trainer_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }
        
        $teamMember = Team::findOrFail($id);
        $trainerId = session('trainer_id');
        
        // Check if belongs to current trainer
        if ($teamMember->trainer_id != $trainerId) {
            return redirect()->back()
                           ->with('error', 'Unauthorized action!');
        }
        
        // Delete from team
        $teamMember->delete();
        
        return redirect()->route('team.index')
                        ->with('success', 'Pokémon removed from team!');
    }
    
    // Catch a Pokémon
    public function catch(Request $request)
    {
        if (!session('logged_in') || !session('trainer_id')) {
            return response()->json([
                'success' => false,
                'error' => 'Please login to catch Pokémon.'
            ], 401);
        }
        
        $request->validate([
            'pokemon_id' => 'required|exists:pokemons,id'
        ]);
        
        $trainerId = session('trainer_id');
        $pokemonId = $request->pokemon_id;
        
        // Check if already caught
        if (Team::where('trainer_id', $trainerId)->where('pokemon_id', $pokemonId)->exists()) {
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
            // Add to team (automatic is_active = true)
            Team::create([
                'trainer_id' => $trainerId,
                'pokemon_id' => $pokemonId,
                'level' => 5,
                'experience' => 0,
                'is_active' => true,
                'date_caught' => now(),
                'caught_location' => 'Wild Encounter'
            ]);
            
            // Update caught count
            $trainer = Trainer::find($trainerId);
            if ($trainer) {
                $trainer->increment('pokemon_caught');
            }
            
            // Record catch history
            \App\Models\CatchHistory::create([
                'trainer_id' => $trainerId,
                'pokemon_id' => $pokemonId,
                'success' => true,
                'location' => 'Wild',
                'method' => 'pokeball'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pokémon caught successfully!',
                'pokemon' => $pokemon
            ]);
        } else {
            // Record failed catch
            \App\Models\CatchHistory::create([
                'trainer_id' => $trainerId,
                'pokemon_id' => $pokemonId,
                'success' => false,
                'location' => 'Wild',
                'method' => 'pokeball'
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Pokémon escaped! Try again.'
            ]);
        }
    }
    
    // Calculate catch chance
    private function calculateCatchChance($pokemon)
    {
        // Base catch rate (40%)
        $catchRate = 40;
        
        // Adjust based on HP (higher HP = harder to catch)
        if ($pokemon->hp) {
            $catchRate *= (100 / ($pokemon->hp + 50));
        }
        
        // Adjust for legendary types
        $types = [];
        if ($pokemon->type1) $types[] = $pokemon->type1;
        if ($pokemon->type2) $types[] = $pokemon->type2;
        
        $legendaryTypes = ['dragon', 'psychic', 'ghost', 'legendary', 'mythical'];
        foreach ($legendaryTypes as $type) {
            if (in_array($type, $types)) {
                $catchRate *= 0.7;
                break;
            }
        }
        
        // Ensure between 5% and 80%
        return max(5, min(80, $catchRate));
    }
    
    // Update team member level
    // Update team member level - SIMPLIFIED VERSION
public function updateLevel(Request $request, $id)
{
    \Log::info('UpdateLevel started', [
        'id' => $id,
        'all_data' => $request->all(),
        'level_param' => $request->input('level'),
        'method' => $request->method()
    ]);

    if (!session('logged_in') || !session('trainer_id')) {
        return response()->json([
            'success' => false,
            'error' => 'Please login first.'
        ], 401);
    }
    
    // For debugging, accept any data
    $level = $request->input('level');
    
    if (!$level) {
        \Log::error('Level parameter missing', $request->all());
        return response()->json([
            'success' => false,
            'error' => 'Level parameter is required.',
            'received_data' => $request->all()
        ]);
    }
    
    $level = (int) $level;
    
    if ($level < 1 || $level > 100) {
        return response()->json([
            'success' => false,
            'error' => 'Level must be between 1 and 100.'
        ]);
    }
    
    $teamMember = Team::find($id);
    
    if (!$teamMember) {
        return response()->json([
            'success' => false,
            'error' => 'Pokémon not found in team.'
        ], 404);
    }
    
    $trainerId = session('trainer_id');
    
    if ($teamMember->trainer_id != $trainerId) {
        return response()->json([
            'success' => false,
            'error' => 'Unauthorized!'
        ], 403);
    }
    
    // Update level and reset experience
    $teamMember->level = $level;
    $teamMember->experience = 0;
    $teamMember->save();
    
    \Log::info('Level updated successfully', [
        'pokemon_id' => $teamMember->pokemon_id,
        'new_level' => $teamMember->level,
        'team_member_id' => $id
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Level updated successfully to ' . $teamMember->level . '!',
        'level' => $teamMember->level,
        'experience' => $teamMember->experience
    ]);
}

public function addExp(Request $request, $id)
{
    if (!session('logged_in') || !session('trainer_id')) {
        return response()->json([
            'success' => false,
            'error' => 'Please login first.'
        ], 401);
    }
    
    $teamMember = Team::with('pokemon')->find($id);
    $trainerId = session('trainer_id');
    
    if (!$teamMember || $teamMember->trainer_id != $trainerId) {
        return response()->json([
            'success' => false,
            'error' => 'Pokémon not found or unauthorized.'
        ]);
    }
    
    // Check daily training limit
    if (TrainingHistory::hasReachedDailyLimit($id)) {
        return response()->json([
            'success' => false,
            'error' => 'Daily training limit reached! You can only train 3 times per day.'
        ]);
    }
    
    $exp = (int) $request->input('exp', 0);
    $trainingType = $request->input('training_type', 'Training');
    
    if ($exp <= 0) {
        return response()->json([
            'success' => false,
            'error' => 'Invalid EXP amount.'
        ]);
    }
    
    $oldLevel = $teamMember->level;
    $oldExp = $teamMember->experience;
    
    // Add experience
    $teamMember->addExperience($exp);
    $teamMember->refresh();
    
    $leveledUp = $teamMember->level > $oldLevel;
    
    // Record training history
    TrainingHistory::create([
        'trainer_id' => $trainerId,
        'team_id' => $id,
        'pokemon_id' => $teamMember->pokemon_id,
        'exp_gained' => $exp,
        'training_type' => $trainingType,
        'old_level' => $oldLevel,
        'new_level' => $teamMember->level,
        'old_experience' => $oldExp,
        'new_experience' => $teamMember->experience,
        'leveled_up' => $leveledUp
    ]);
    
    // Get remaining trainings
    $remainingTrainings = TrainingHistory::getRemainingTrainings($id);
    
    \Log::info("Training: {$teamMember->display_name} received {$exp} EXP from {$trainingType}");
    
    return response()->json([
        'success' => true,
        'message' => "Training complete! Added {$exp} EXP",
        'pokemon_name' => $teamMember->display_name,
        'exp_added' => $exp,
        'training_type' => $trainingType,
        'old_level' => $oldLevel,
        'new_level' => $teamMember->level,
        'old_experience' => $oldExp,
        'new_experience' => $teamMember->experience,
        'experience_for_next_level' => $teamMember->experience_for_next_level,
        'leveled_up' => $leveledUp,
        'daily_limit' => [
            'remaining' => $remainingTrainings,
            'max' => 3
        ]
    ]);
}

// Add new method to get training history
public function getTrainingHistory($id)
{
    if (!session('logged_in') || !session('trainer_id')) {
        return response()->json([
            'success' => false,
            'error' => 'Please login first.'
        ], 401);
    }
    
    $teamMember = Team::find($id);
    $trainerId = session('trainer_id');
    
    if (!$teamMember || $teamMember->trainer_id != $trainerId) {
        return response()->json([
            'success' => false,
            'error' => 'Pokémon not found or unauthorized.'
        ]);
    }
    
    $history = TrainingHistory::where('team_id', $id)
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
    
    $remaining = TrainingHistory::getRemainingTrainings($id);
    
    return response()->json([
        'success' => true,
        'history' => $history,
        'daily_limit' => [
            'remaining' => $remaining,
            'max' => 3,
            'used' => 3 - $remaining
        ]
    ]);
}
}