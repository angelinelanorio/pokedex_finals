<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catch_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained()->onDelete('cascade');
            $table->foreignId('pokemon_id')->constrained('pokemons')->onDelete('cascade');
            $table->boolean('success')->default(true);
            $table->string('location')->nullable();
            $table->string('method')->default('pokeball');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catch_history');
    }
};