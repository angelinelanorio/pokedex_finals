<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            // 1. Gawing nullable ang existing foreign key
            $table->dropForeign(['trainer2_id']);
            $table->unsignedBigInteger('trainer2_id')->nullable()->change();
            
            // 2. Magdagdag ng bagong column para sa AI trainers
            $table->unsignedBigInteger('ai_trainer_id')->nullable()->after('trainer2_id');
            $table->foreign('ai_trainer_id')->references('id')->on('ai_trainers')->onDelete('cascade');
            
            // 3. Magdagdag ng composite index
            $table->index(['trainer2_id', 'ai_trainer_id']);
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropForeign(['ai_trainer_id']);
            $table->dropIndex(['trainer2_id', 'ai_trainer_id']);
            $table->dropColumn('ai_trainer_id');
            
            $table->unsignedBigInteger('trainer2_id')->nullable(false)->change();
            $table->foreign('trainer2_id')->references('id')->on('trainers')->onDelete('cascade');
        });
    }
};