<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained()->onDelete('cascade');
            $table->foreignId('pokemon_id')->constrained('pokemons')->onDelete('cascade');
            $table->string('nickname')->nullable();
            $table->integer('level')->default(5);
            $table->integer('experience')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('date_caught')->default(now());
            $table->string('caught_location')->nullable();
            $table->timestamps();
            
            $table->unique(['trainer_id', 'pokemon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};