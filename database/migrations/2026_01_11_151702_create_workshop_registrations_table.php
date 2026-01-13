<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshop_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('alumni_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('status')->default('registered'); 
            $table->timestamps();

            $table->unique(['workshop_id', 'alumni_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_registrations');
    }
};
