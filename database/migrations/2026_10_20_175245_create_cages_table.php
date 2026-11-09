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
        Schema::create('cages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('location')->nullable();
            $table->unsignedInteger('capacity')->default(1);
            $table->enum('status', ['active', 'available', 'maintenance'])->default('active');
            $table->timestamps();
        });

        Schema::table('birds', function (Blueprint $table) {
            $table->foreign('cage_id')->references('id')->on('cages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('birds', function (Blueprint $table) {
            $table->dropForeign(['cage_id']);
        });

        Schema::dropIfExists('cages');
    }
};
