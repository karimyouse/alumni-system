<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('major')->nullable();
            $table->string('graduation_year')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->text('bio')->nullable();
            $table->text('skills')->nullable(); // comma separated
            $table->string('linkedin')->nullable();
            $table->string('portfolio')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
