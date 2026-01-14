<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('region')->default('Kanto');
            $table->integer('level')->default(1);
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->integer('pokemon_caught')->default(0);
            $table->integer('trades_completed')->default(0);
            $table->integer('badges_earned')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};