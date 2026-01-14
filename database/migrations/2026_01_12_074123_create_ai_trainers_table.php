<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('region')->default('Kanto');
            $table->integer('level')->default(5);
            $table->text('description')->nullable();
            $table->string('avatar_color')->default('#3498db');
            $table->integer('pokemon_count')->default(0);
            $table->integer('trades_count')->default(0);
            $table->json('trade_preferences')->nullable();
            $table->json('personality')->nullable();
            $table->json('team_data')->nullable();
            $table->timestamps();
        });
        
        // Insert some AI trainers
        $this->seedAITrainers();
    }
    
    private function seedAITrainers(): void
    {
        $aiTrainers = [
            [
                'name' => 'Brock',
                'region' => 'Kanto',
                'level' => 15,
                'description' => 'Rock-type Gym Leader. Strong and reliable!',
                'avatar_color' => '#e74c3c',
                'pokemon_count' => 8,
                'trades_count' => 5,
                'trade_preferences' => json_encode([
                    'wants' => ['rock', 'ground', 'fighting'],
                    'offers' => ['rock', 'ground'],
                    'fairness' => 'medium',
                    'minLevel' => 5,
                    'tradeStyle' => 'balanced'
                ]),
                'personality' => json_encode([
                    'patience' => 7,
                    'generosity' => 6,
                    'stubbornness' => 8
                ]),
                'team_data' => json_encode([
                    ['id' => 74, 'name' => 'Geodude'],  // Rock/Ground
                    ['id' => 95, 'name' => 'Onix'],     // Rock/Ground
                    ['id' => 111, 'name' => 'Rhyhorn']  // Ground/Rock
                ])
            ],
            [
                'name' => 'Misty',
                'region' => 'Kanto',
                'level' => 14,
                'description' => 'Water-type Gym Leader. Loves water Pokémon!',
                'avatar_color' => '#3498db',
                'pokemon_count' => 7,
                'trades_count' => 3,
                'trade_preferences' => json_encode([
                    'wants' => ['water', 'electric', 'psychic'],
                    'offers' => ['water', 'fairy'],
                    'fairness' => 'strict',
                    'minLevel' => 5,
                    'tradeStyle' => 'generous'
                ]),
                'personality' => json_encode([
                    'patience' => 5,
                    'generosity' => 8,
                    'stubbornness' => 6
                ]),
                'team_data' => json_encode([
                    ['id' => 120, 'name' => 'Staryu'],   // Water
                    ['id' => 121, 'name' => 'Starmie'],  // Water/Psychic
                    ['id' => 54, 'name' => 'Psyduck']    // Water
                ])
            ],
            [
                'name' => 'Ash',
                'region' => 'Kanto',
                'level' => 10,
                'description' => 'Aspiring Pokémon Master. Will trade for almost anything!',
                'avatar_color' => '#f1c40f',
                'pokemon_count' => 6,
                'trades_count' => 2,
                'trade_preferences' => json_encode([
                    'wants' => ['electric', 'flying', 'fire'],
                    'offers' => ['normal', 'flying'],
                    'fairness' => 'lenient',
                    'minLevel' => 3,
                    'tradeStyle' => 'random'
                ]),
                'personality' => json_encode([
                    'patience' => 8,
                    'generosity' => 9,
                    'stubbornness' => 3
                ]),
                'team_data' => json_encode([
                    ['id' => 25, 'name' => 'Pikachu'],   // Electric
                    ['id' => 16, 'name' => 'Pidgey'],    // Normal/Flying
                    ['id' => 10, 'name' => 'Caterpie']   // Bug
                ])
            ]
        ];
        
        foreach ($aiTrainers as $trainer) {
            DB::table('ai_trainers')->insert($trainer);
        }
    }
    
    public function down(): void
    {
        Schema::dropIfExists('ai_trainers');
    }
};