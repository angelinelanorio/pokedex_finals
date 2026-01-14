<?php

namespace Database\Seeders;

use App\Models\AITrainer;
use Illuminate\Database\Seeder;

class PerfectAITrainersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🔄 Creating PERFECT AI trainers for your Pokémon...');
        
        // Clear existing AI trainers
        AITrainer::truncate();
        
        // ==================== ITO ANG POKÉMON MO ====================
        $yourPokemonIds = [15, 18, 19, 20, 21]; // Pikachu, Bulbasaur, Charmander, Squirtle, Gengar
        
        // 1. BROCK - High level trainer (for when you have high level Pokémon)
        AITrainer::create([
            'id' => 101,
            'name' => 'Brock',
            'region' => 'Kanto',
            'level' => 15,
            'description' => 'Rock-type Gym Leader. Has strong Pokémon!',
            'avatar_color' => '#e74c3c',
            'pokemon_count' => 8,
            'trades_count' => 5,
            'team_data' => [
                ['pokemon_id' => 15, 'level' => 35], // Pikachu Level 35
                ['pokemon_id' => 18, 'level' => 33], // Bulbasaur Level 33  
                ['pokemon_id' => 19, 'level' => 32], // Charmander Level 32
            ]
        ]);
        
        // 2. MISTY - Medium level trainer
        AITrainer::create([
            'id' => 102,
            'name' => 'Misty',
            'region' => 'Kanto',
            'level' => 14,
            'description' => 'Water-type Gym Leader. Good for mid-level trades!',
            'avatar_color' => '#3498db',
            'pokemon_count' => 7,
            'trades_count' => 3,
            'team_data' => [
                ['pokemon_id' => 20, 'level' => 28], // Squirtle Level 28
                ['pokemon_id' => 15, 'level' => 30], // Pikachu Level 30
                ['pokemon_id' => 21, 'level' => 25], // Gengar Level 25
            ]
        ]);
        
        // 3. ASH - Low level trainer (BEGINNER FRIENDLY!)
        AITrainer::create([
            'id' => 103,
            'name' => 'Ash',
            'region' => 'Kanto',
            'level' => 10,
            'description' => 'Aspiring Pokémon Master. Perfect for beginners!',
            'avatar_color' => '#f1c40f',
            'pokemon_count' => 6,
            'trades_count' => 2,
            'team_data' => [
                ['pokemon_id' => 15, 'level' => 18], // Pikachu Level 18
                ['pokemon_id' => 18, 'level' => 16], // Bulbasaur Level 16
                ['pokemon_id' => 19, 'level' => 14], // Charmander Level 14
            ]
        ]);
        
        // 4. GARY - VERY LOW level trainer (SUPER BEGINNER!)
        AITrainer::create([
            'id' => 104,
            'name' => 'Gary',
            'region' => 'Kanto',
            'level' => 8,
            'description' => 'Beginner trainer with low-level Pokémon. Easy trades!',
            'avatar_color' => '#9b59b6',
            'pokemon_count' => 4,
            'trades_count' => 1,
            'team_data' => [
                ['pokemon_id' => 15, 'level' => 12], // Pikachu Level 12
                ['pokemon_id' => 18, 'level' => 10], // Bulbasaur Level 10
                ['pokemon_id' => 19, 'level' => 8],  // Charmander Level 8
            ]
        ]);
        
        // 5. NEW TRAINER: OAK - Balanced for all levels
        AITrainer::create([
            'id' => 105,
            'name' => 'Professor Oak',
            'region' => 'Kanto',
            'level' => 20,
            'description' => 'Pokémon Professor. Has balanced Pokémon for fair trades!',
            'avatar_color' => '#2ecc71',
            'pokemon_count' => 10,
            'trades_count' => 8,
            'team_data' => [
                ['pokemon_id' => 20, 'level' => 22], // Squirtle Level 22
                ['pokemon_id' => 21, 'level' => 20], // Gengar Level 20
                ['pokemon_id' => 15, 'level' => 25], // Pikachu Level 25
            ]
        ]);
        AITrainer::create([
    'id' => 106,
    'name' => 'Beginner Bot',
    'region' => 'Kanto',
    'level' => 5,
    'description' => 'Perfect for new trainers! Has Level 1-5 Pokémon only.',
    'avatar_color' => '#1abc9c',
    'pokemon_count' => 3,
    'trades_count' => 0,
    'team_data' => [
        ['pokemon_id' => 15, 'level' => 6], // Pikachu Level 5
        ['pokemon_id' => 18, 'level' => 2], // Bulbasaur Level 2
        ['pokemon_id' => 19, 'level' => 1], // Charmander Level 1
    ]
]);
        
        $this->command->info('🎉 PERFECT AI Trainers created!');
        $this->command->info('📊 Pokémon IDs used: 15(Pikachu), 18(Bulbasaur), 19(Charmander), 20(Squirtle), 21(Gengar)');
    }
    
}