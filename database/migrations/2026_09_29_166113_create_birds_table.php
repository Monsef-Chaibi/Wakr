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
        Schema::create('birds', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('species');

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('sex', ['male', 'female', 'unknown'])
                ->default('unknown');

            $table->foreignId('cage_id')->nullable();

            $table->enum('status', [
                'active',
                'breeding',
                'sick',
                'retired',
                'deceased',
            ])->default('active');

            $table->string('band_number')->nullable()->unique();
            $table->string('color_mutation')->nullable();
            $table->date('birth_date')->nullable();

            $table->foreignId('parent_male_id')
                ->nullable()
                ->constrained('birds')
                ->nullOnDelete();

            $table->foreignId('parent_female_id')
                ->nullable()
                ->constrained('birds')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birds');
    }
};
