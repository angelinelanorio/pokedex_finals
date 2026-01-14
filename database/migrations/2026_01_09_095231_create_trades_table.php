<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer1_id')->constrained('trainers')->onDelete('cascade');
            $table->foreignId('trainer2_id')->constrained('trainers')->onDelete('cascade');
            $table->foreignId('pokemon1_id')->constrained('pokemons')->onDelete('cascade');
            $table->foreignId('pokemon2_id')->constrained('pokemons')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};