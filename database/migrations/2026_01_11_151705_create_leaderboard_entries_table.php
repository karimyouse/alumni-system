<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumni_user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedInteger('rank')->default(0);
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('activities')->default(0);
            $table->string('trend')->nullable(); 
            $table->string('period')->default('monthly');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_entries');
    }
};
