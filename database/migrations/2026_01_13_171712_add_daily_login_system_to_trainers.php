<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            // Daily login system (SIMPLE)
            $table->integer('daily_streak')->default(0)->after('badges_earned');
            $table->date('last_login_date')->nullable()->after('daily_streak');
            $table->integer('total_logins')->default(0)->after('last_login_date');
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn(['daily_streak', 'last_login_date', 'total_logins']);
        });
    }
};