<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pokemon;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    // Show all trainers
    public function index(Request $request)
    {
        $query = Trainer::query();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('region', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by region
        if ($request->has('region') && $request->region != '') {
            $query->where('region', $request->region);
        }
        
        // Filter by level
        if ($request->has('min_level')) {
            $query->where('level', '>=', $request->min_level);
        }
        
        if ($request->has('max_level')) {
            $query->where('level', '<=', $request->max_level);
        }
        
        // Order by level (highest first) or name
        $orderBy = $request->has('order_by') ? $request->order_by : 'level';
        $orderDirection = $request->has('order_dir') ? $request->order_dir : 'desc';
        
        $query->orderBy($orderBy, $orderDirection);
        
        $trainers = $query->withCount(['teams as active_team_count' => function($query) {
            $query->where('is_active', true);
        }])
        ->paginate(20);
        
        // Get unique regions for filter
        $regions = Trainer::select('region')->distinct()->pluck('region');
        
        return view('trainers.index', compact('trainers', 'regions'));
    }
    
    // Show trainer profile
    public function show($id)
    {
        $trainer = Trainer::withCount(['teams as total_caught'])
                         ->with(['teams' => function($query) {
                             $query->where('is_active', true)
                                   ->with('pokemon')
                                   ->orderBy('level', 'desc');
                         }])
                         ->findOrFail($id);
        
        // Calculate stats
        $stats = $this->calculateTrainerStats($trainer);
        
        // Get recent activity
        $recentActivity = $this->getTrainerActivity($trainer);
        
        // Get trade history
        $tradeHistory = \App\Models\Trade::where(function($query) use ($trainer) {
            $query->where('trainer1_id', $trainer->id)
                  ->orWhere('trainer2_id', $trainer->id);
        })
        ->with(['trainer1', 'trainer2', 'pokemon1', 'pokemon2'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
        
        // Check if viewing own profile
        $isOwnProfile = Auth::check() && Auth::id() == $trainer->id;
        
        return view('trainers.show', compact('trainer', 'stats', 'recentActivity', 'tradeHistory', 'isOwnProfile'));
    }
    
    // Show create trainer form
    public function create()
    {
        return view('trainers.create');
    }
    
    // Store new trainer
    public function store(Request $request)
    {
        // Only allow creating trainers if not logged in or admin
        if (Auth::check() && !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can create new trainers!');
        }
        
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:trainers',
            'email' => 'required|email|unique:trainers',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'region' => 'required|string|max:50',
            'level' => 'integer|min:1|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        // Handle avatar upload
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }
        
        $trainer = Trainer::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'region' => $validated['region'],
            'level' => $validated['level'] ?? 1,
            'bio' => $validated['bio'] ?? null,
            'avatar_url' => $avatarPath ? Storage::url($avatarPath) : null
        ]);
        
        // Auto-generate a starter team
        $this->generateStarterTeam($trainer);
        
        $redirectRoute = Auth::check() ? 'trainers.show' : 'login';
        $redirectParam = Auth::check() ? $trainer->id : null;
        
        return redirect()->route($redirectRoute, $redirectParam)
                        ->with('success', 'Trainer created successfully!');
    }
    
    // Show edit trainer form
    public function edit($id)
    {
        $trainer = Trainer::findOrFail($id);
        
        // Check authorization
        if (!Auth::check() || (Auth::id() != $trainer->id && !Auth::user()->is_admin)) {
            return redirect()->back()
                           ->with('error', 'Unauthorized to edit this profile!');
        }
        
        return view('trainers.edit', compact('trainer'));
    }
    
    // Update trainer
    public function update(Request $request, $id)
    {
        $trainer = Trainer::findOrFail($id);
        
        // Check authorization
        if (!Auth::check() || (Auth::id() != $trainer->id && !Auth::user()->is_admin)) {
            return redirect()->back()
                           ->with('error', 'Unauthorized to update this profile!');
        }
        
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'region' => 'required|string|max:50',
            'level' => 'integer|min:1|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed'
        ]);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($trainer->avatar_url) {
                $oldPath = str_replace('/storage/', '', $trainer->avatar_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_url'] = Storage::url($avatarPath);
        }
        
        // Update password if provided
        if ($request->has('new_password')) {
            if (!Hash::check($request->current_password, $trainer->password)) {
                return redirect()->back()
                               ->with('error', 'Current password is incorrect!');
            }
            
            $validated['password'] = Hash::make($request->new_password);
        }
        
        $trainer->update($validated);
        
        return redirect()->route('trainers.show', $trainer->id)
                        ->with('success', 'Profile updated successfully!');
    }
    
    // Delete trainer
    public function destroy($id)
    {
        $trainer = Trainer::findOrFail($id);
        
        // Only allow deletion if admin or own account
        if (!Auth::check() || (Auth::id() != $trainer->id && !Auth::user()->is_admin)) {
            return redirect()->back()
                           ->with('error', 'Unauthorized to delete this account!');
        }
        
        // Delete avatar if exists
        if ($trainer->avatar_url) {
            $path = str_replace('/storage/', '', $trainer->avatar_url);
            Storage::disk('public')->delete($path);
        }
        
        // Delete trainer (cascade will delete related records)
        $trainer->delete();
        
        if (Auth::id() == $trainer->id) {
            // If deleting own account, logout and redirect to home
            Auth::logout();
            return redirect()->route('home')
                            ->with('success', 'Your account has been deleted.');
        }
        
        return redirect()->route('trainers.index')
                        ->with('success', 'Trainer deleted successfully!');
    }
    
    // Show trainer's team
    public function showTeam($id)
    {
        $trainer = Trainer::with(['teams' => function($query) {
            $query->where('is_active', true)
                  ->with('pokemon')
                  ->orderBy('level', 'desc');
        }])
        ->findOrFail($id);
        
        $teamStats = $this->calculateTeamStats($trainer->teams);
        
        return view('trainers.team', compact('trainer', 'teamStats'));
    }
    
    // Show trainer's Pokédex progress
    public function showPokedex($id)
    {
        $trainer = Trainer::findOrFail($id);
        $progress = $trainer->pokedex_progress;
        
        // Get caught Pokémon
        $caughtPokemon = $trainer->teams()
                                ->with('pokemon')
                                ->get()
                                ->pluck('pokemon');
        
        // Get missing Pokémon
        $allPokemon = Pokemon::orderBy('pokedex_number')->get();
        $missingPokemon = $allPokemon->filter(function($pokemon) use ($caughtPokemon) {
            return !$caughtPokemon->contains('id', $pokemon->id);
        });
        
        return view('trainers.pokedex', compact('trainer', 'progress', 'caughtPokemon', 'missingPokemon'));
    }
    
    // Show trainer's trade history
    public function showTradeHistory($id)
    {
        $trainer = Trainer::findOrFail($id);
        
        $trades = \App\Models\Trade::where(function($query) use ($trainer) {
            $query->where('trainer1_id', $trainer->id)
                  ->orWhere('trainer2_id', $trainer->id);
        })
        ->with(['trainer1', 'trainer2', 'pokemon1', 'pokemon2'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);
        
        // Calculate trade stats
        $tradeStats = [
            'total_trades' => $trades->total(),
            'unique_partners' => \App\Models\Trade::where(function($query) use ($trainer) {
                $query->where('trainer1_id', $trainer->id)
                      ->orWhere('trainer2_id', $trainer->id);
            })
            ->selectRaw('CASE WHEN trainer1_id = ? THEN trainer2_id ELSE trainer1_id END as partner_id', [$trainer->id])
            ->distinct()
            ->count()
        ];
        
        return view('trainers.trades', compact('trainer', 'trades', 'tradeStats'));
    }
    
    // API: Get trainer data
    public function apiShow($id)
    {
        $trainer = Trainer::withCount(['teams as total_caught'])
                         ->with(['teams' => function($query) {
                             $query->where('is_active', true)
                                   ->with('pokemon');
                         }])
                         ->find($id);
        
        if (!$trainer) {
            return response()->json([
                'success' => false,
                'error' => 'Trainer not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'trainer' => $trainer
        ]);
    }
    
    // API: Get all trainers
    public function apiIndex(Request $request)
    {
        $query = Trainer::query();
        
        if ($request->has('search')) {
            $query->where('username', 'LIKE', "%{$request->search}%");
        }
        
        if ($request->has('region')) {
            $query->where('region', $request->region);
        }
        
        $trainers = $query->withCount(['teams as total_caught'])
                         ->orderBy('level', 'desc')
                         ->get();
        
        return response()->json([
            'success' => true,
            'count' => $trainers->count(),
            'trainers' => $trainers
        ]);
    }
    
    // API: Update trainer level
    public function updateLevel(Request $request, $id)
    {
        if (!Auth::check() || Auth::id() != $id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }
        
        $request->validate([
            'level' => 'required|integer|min:1|max:100'
        ]);
        
        $trainer = Trainer::findOrFail($id);
        $trainer->update(['level' => $request->level]);
        
        return response()->json([
            'success' => true,
            'message' => 'Level updated!',
            'trainer' => $trainer
        ]);
    }
    
    // Calculate trainer statistics
    private function calculateTrainerStats($trainer)
    {
        $team = $trainer->teams;
        
        return [
            'total_caught' => $trainer->teams()->count(),
            'active_team_size' => $team->count(),
            'total_power' => $team->sum(function($member) {
                return $member->pokemon->total_stats;
            }),
            'average_level' => $team->avg('level') ?? 0,
            'highest_level' => $team->max('level') ?? 0,
            'types_covered' => $team->flatMap(function($member) {
                return $member->pokemon->types;
            })->unique()->count(),
            'pokedex_percentage' => $trainer->pokedex_progress['percentage']
        ];
    }
    
    // Calculate team statistics
    private function calculateTeamStats($team)
    {
        return [
            'count' => $team->count(),
            'total_power' => $team->sum(function($member) {
                return $member->pokemon->total_stats;
            }),
            'average_level' => $team->avg('level') ?? 0,
            'highest_level' => $team->max('level') ?? 0,
            'types_covered' => $team->flatMap(function($member) {
                return $member->pokemon->types;
            })->unique()->count(),
            'total_hp' => $team->sum(function($member) {
                return $member->pokemon->hp;
            }),
            'total_attack' => $team->sum(function($member) {
                return $member->pokemon->attack;
            }),
            'total_defense' => $team->sum(function($member) {
                return $member->pokemon->defense;
            })
        ];
    }
    
    // Get trainer activity
    private function getTrainerActivity($trainer)
    {
        $activities = collect();
        
        // Recent catches
        $recentCatches = $trainer->teams()
                                ->with('pokemon')
                                ->orderBy('date_caught', 'desc')
                                ->limit(3)
                                ->get()
                                ->map(function($team) {
                                    return [
                                        'type' => 'catch',
                                        'icon' => 'fas fa-pokeball',
                                        'color' => '#e74c3c',
                                        'message' => "Caught {$team->pokemon->name}",
                                        'time' => $team->date_caught->diffForHumans()
                                    ];
                                });
        
        $activities = $activities->merge($recentCatches);
        
        // Recent level ups
        $recentLevelUps = $trainer->teams()
                                 ->where('level', '>', 5)
                                 ->with('pokemon')
                                 ->orderBy('updated_at', 'desc')
                                 ->limit(2)
                                 ->get()
                                 ->map(function($team) {
                                     return [
                                         'type' => 'level_up',
                                         'icon' => 'fas fa-arrow-up',
                                         'color' => '#2ecc71',
                                         'message' => "{$team->pokemon->name} reached level {$team->level}",
                                         'time' => $team->updated_at->diffForHumans()
                                     ];
                                 });
        
        $activities = $activities->merge($recentLevelUps);
        
        // Recent trades
        $recentTrades = \App\Models\Trade::where(function($query) use ($trainer) {
            $query->where('trainer1_id', $trainer->id)
                  ->orWhere('trainer2_id', $trainer->id);
        })
        ->with(['pokemon1', 'pokemon2'])
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get()
        ->map(function($trade) use ($trainer) {
            $receivedPokemon = $trade->trainer1_id == $trainer->id ? $trade->pokemon2 : $trade->pokemon1;
            $givenPokemon = $trade->trainer1_id == $trainer->id ? $trade->pokemon1 : $trade->pokemon2;
            
            return [
                'type' => 'trade',
                'icon' => 'fas fa-exchange-alt',
                'color' => '#3498db',
                'message' => "Traded {$givenPokemon->name} for {$receivedPokemon->name}",
                'time' => $trade->created_at->diffForHumans()
            ];
        });
        
        $activities = $activities->merge($recentTrades);
        
        // Sort by time (most recent first)
        return $activities->sortByDesc(function($activity) {
            return strtotime($activity['time']);
        })->values()->take(5);
    }
    
    // Generate starter team for new trainer
    private function generateStarterTeam($trainer)
    {
        $starterPokemon = Pokemon::whereIn('pokedex_number', [1, 4, 7, 25, 133, 147])
                                ->inRandomOrder()
                                ->limit(3)
                                ->get();
        
        foreach ($starterPokemon as $pokemon) {
            Team::create([
                'trainer_id' => $trainer->id,
                'pokemon_id' => $pokemon->id,
                'level' => rand(5, 15),
                'experience' => 0,
                'is_active' => true,
                'date_caught' => now(),
                'caught_location' => 'Starter Pokémon'
            ]);
        }
        
        // Update caught count
        $trainer->update(['pokemon_caught' => 3]);
    }
    
    // Add Pokémon to trainer's collection (admin function)
    public function addPokemon(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can perform this action!');
        }
        
        $request->validate([
            'pokemon_id' => 'required|exists:pokemons,id',
            'level' => 'integer|min:1|max:100'
        ]);
        
        $trainer = Trainer::findOrFail($id);
        $pokemonId = $request->pokemon_id;
        
        // Check if already caught
        if ($trainer->teams()->where('pokemon_id', $pokemonId)->exists()) {
            return redirect()->back()
                           ->with('error', 'Trainer already has this Pokémon!');
        }
        
        Team::create([
            'trainer_id' => $trainer->id,
            'pokemon_id' => $pokemonId,
            'level' => $request->level ?? 5,
            'experience' => 0,
            'is_active' => false,
            'date_caught' => now(),
            'caught_location' => 'Admin Grant'
        ]);
        
        // Update caught count
        $trainer->increment('pokemon_caught');
        
        return redirect()->back()
                        ->with('success', 'Pokémon added to trainer\'s collection!');
    }
    
    // Remove Pokémon from trainer's collection (admin function)
    public function removePokemon(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can perform this action!');
        }
        
        $request->validate([
            'team_id' => 'required|exists:teams,id'
        ]);
        
        $teamMember = Team::findOrFail($request->team_id);
        
        // Verify it belongs to the trainer
        if ($teamMember->trainer_id != $id) {
            return redirect()->back()
                           ->with('error', 'This Pokémon does not belong to this trainer!');
        }
        
        $teamMember->delete();
        
        // Update caught count
        $trainer = Trainer::find($id);
        $trainer->decrement('pokemon_caught');
        
        return redirect()->back()
                        ->with('success', 'Pokémon removed from trainer\'s collection!');
    }
    
    // Promote trainer to admin (admin function)
    public function promoteToAdmin($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can perform this action!');
        }
        
        $trainer = Trainer::findOrFail($id);
        $trainer->update(['is_admin' => true]);
        
        return redirect()->back()
                        ->with('success', 'Trainer promoted to administrator!');
    }
    
    // Demote trainer from admin (admin function)
    public function demoteFromAdmin($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can perform this action!');
        }
        
        $trainer = Trainer::findOrFail($id);
        
        // Prevent demoting self
        if ($trainer->id == Auth::id()) {
            return redirect()->back()
                           ->with('error', 'You cannot demote yourself!');
        }
        
        $trainer->update(['is_admin' => false]);
        
        return redirect()->back()
                        ->with('success', 'Trainer demoted from administrator!');
    }
    
    // Reset trainer password (admin function)
    public function resetPassword(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can perform this action!');
        }
        
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed'
        ]);
        
        $trainer = Trainer::findOrFail($id);
        $trainer->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        return redirect()->back()
                        ->with('success', 'Password reset successfully!');
    }
    
    // Export trainer data (admin function)
    public function exportData($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->back()
                           ->with('error', 'Only administrators can perform this action!');
        }
        
        $trainer = Trainer::with(['teams.pokemon'])->findOrFail($id);
        
        $data = [
            'trainer' => [
                'id' => $trainer->id,
                'username' => $trainer->username,
                'email' => $trainer->email,
                'full_name' => $trainer->full_name,
                'region' => $trainer->region,
                'level' => $trainer->level,
                'pokemon_caught' => $trainer->pokemon_caught,
                'trades_completed' => $trainer->trades_completed,
                'badges_earned' => $trainer->badges_earned,
                'created_at' => $trainer->created_at,
                'updated_at' => $trainer->updated_at
            ],
            'team' => $trainer->teams->map(function($team) {
                return [
                    'pokemon' => $team->pokemon->name,
                    'pokedex_number' => $team->pokemon->pokedex_number,
                    'level' => $team->level,
                    'date_caught' => $team->date_caught,
                    'caught_location' => $team->caught_location
                ];
            }),
            'pokedex_progress' => $trainer->pokedex_progress
        ];
        
        // Generate JSON file
        $filename = "trainer_{$trainer->username}_" . date('Y-m-d') . ".json";
        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ]);
    }
}