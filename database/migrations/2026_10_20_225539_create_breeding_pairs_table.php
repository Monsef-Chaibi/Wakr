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
        Schema::create('breeding_pairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('male_bird_id')->constrained('birds')->cascadeOnDelete();
            $table->foreignId('female_bird_id')->constrained('birds')->cascadeOnDelete();
            $table->unsignedInteger('eggs')->default(0);
            $table->enum('status', ['ready', 'nesting', 'hatching'])->default('ready');
            $table->date('next_milestone')->nullable();
            $table->timestamps();
            $table->unique(['male_bird_id', 'female_bird_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breeding_pairs');
    }
};
