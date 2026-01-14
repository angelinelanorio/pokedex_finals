<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('pokemon_id')->constrained('pokemons')->onDelete('cascade');
            $table->integer('exp_gained');
            $table->string('training_type'); // easy, medium, hard
            $table->integer('old_level');
            $table->integer('new_level');
            $table->integer('old_experience');
            $table->integer('new_experience');
            $table->boolean('leveled_up')->default(false);
            $table->timestamps();
            
            // Index for faster daily limit checks
            $table->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_histories');
    }
};