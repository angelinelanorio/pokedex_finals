<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            $table->integer('pokedex_number')->unique();
            $table->string('name', 100);
            $table->string('type1', 20);
            $table->string('type2', 20)->nullable();
            $table->decimal('height', 5, 2)->default(0.70);
            $table->decimal('weight', 5, 2)->default(6.90);
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->text('abilities')->nullable();
            $table->text('moves')->nullable();
            
            // Stats
            $table->integer('hp')->default(50);
            $table->integer('attack')->default(50);
            $table->integer('defense')->default(50);
            $table->integer('special_attack')->default(65);
            $table->integer('special_defense')->default(65);
            $table->integer('speed')->default(45);
            
            // Evolution
            $table->integer('evolution_stage')->default(1);
            $table->integer('evolves_from')->nullable();
            $table->integer('evolves_to')->nullable();
            $table->string('evolution_condition')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }
};