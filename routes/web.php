<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CatchController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ============ AUTH ROUTES ============
// Login Routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Register Routes
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// =====================================

// Protected routes (require session check)
Route::middleware(['web'])->group(function () {
    // Pokédex Routes
    Route::get('/pokedex', [PokemonController::class, 'index'])->name('pokemon.index');
    Route::get('/pokemon/create', [PokemonController::class, 'create'])->name('pokemon.create');
    Route::post('/pokemon', [PokemonController::class, 'store'])->name('pokemon.store');
    Route::get('/pokemon/{id}', [PokemonController::class, 'show'])->name('pokemon.show');
    Route::get('/pokemon/{id}/edit', [PokemonController::class, 'edit'])->name('pokemon.edit');
    Route::put('/pokemon/{id}', [PokemonController::class, 'update'])->name('pokemon.update');
    Route::delete('/pokemon/{id}', [PokemonController::class, 'destroy'])->name('pokemon.destroy');
    Route::get('/pokemon-catch', [PokemonController::class, 'catchGame'])->name('pokemon.catch');

    // Catch Game Routes
    Route::get('/catch', [CatchController::class, 'index'])->name('catch.index');
    Route::post('/catch/attempt', [CatchController::class, 'attemptCatch'])->name('catch.attempt');
    Route::get('/catch/random', [CatchController::class, 'getRandomPokemon'])->name('catch.random');
    Route::get('/catch/stats', [CatchController::class, 'getStats'])->name('catch.stats');

    // ============ TEAM ROUTES (FIXED) ============
    Route::get('/team', [TeamController::class, 'index'])->name('team.index');

    // Para sa store method (add to team)
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');

    // Para sa destroy method (remove from team)
    Route::delete('/team/{team}', [TeamController::class, 'destroy'])->name('team.destroy');

    // Para sa updateLevel method
    Route::put('/team/{team}/level', [TeamController::class, 'updateLevel'])->name('team.updateLevel');

    // Para sa catch method (ajax catch)
    Route::post('/team/catch', [TeamController::class, 'catch'])->name('team.catch');
    // =============================================

    Route::post('/team/{team}/add-exp', [TeamController::class, 'addExp'])->name('team.addExp');
    Route::get('/team/{team}/training-history', [TeamController::class, 'getTrainingHistory'])->name('team.trainingHistory');

    // Trading
    Route::get('/trading', [TradeController::class, 'index'])->name('trading.index');
    Route::post('/trading', [TradeController::class, 'store'])->name('trading.store');
    Route::post('/trading/select-trainer', [TradeController::class, 'selectTrainer'])->name('trading.selectTrainer');
    Route::get('/trading/clear-selection', [TradeController::class, 'clearSelection'])->name('trading.clearSelection');
    Route::post('/trading/get-ai-offer', [TradeController::class, 'getAIOffer'])->name('trading.getAIOffer');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    // Daily login route
    Route::post('/profile/daily-login', [ProfileController::class, 'dailyLogin'])->name('profile.daily-login');

    Route::get('/trading/force-team', function () {
        $userId = session('trainer_id');

        // Force add Pokémon to team if none
        $teamCount = \App\Models\Team::where('trainer_id', $userId)
            ->where('is_active', true)
            ->count();

        if ($teamCount == 0) {
            // Add default Pokémon to team
            \App\Models\Team::create([
                'trainer_id' => $userId,
                'pokemon_id' => 15, // Pikachu
                'level' => 1,
                'is_active' => true,
                'date_caught' => now()
            ]);

            return redirect()->route('trading.index')
                ->with('success', 'Pikachu added to your team!');
        }

        return redirect()->route('trading.index');
    })->name('trading.forceTeam');
});
