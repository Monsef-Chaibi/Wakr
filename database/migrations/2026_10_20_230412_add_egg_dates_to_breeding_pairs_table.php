<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('breeding_pairs', function (Blueprint $table) {
            Schema::table('breeding_pairs', function (Blueprint $table): void {
                $table->json('egg_dates')->nullable()->after('eggs');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('breeding_pairs', function (Blueprint $table) {
            Schema::table('breeding_pairs', function (Blueprint $table): void {
                $table->dropColumn('egg_dates');
            });
        });
    }
};
