<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organizer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('organizer_role')->nullable();

            $table->string('title');
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('location')->nullable();

            $table->unsignedInteger('spots')->default(0);
            $table->unsignedInteger('max_spots')->default(0);

            $table->string('status')->default('upcoming'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
