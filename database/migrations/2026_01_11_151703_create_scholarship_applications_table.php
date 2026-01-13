<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scholarship_id')->constrained('scholarships')->cascadeOnDelete();
            $table->foreignId('alumni_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('status')->default('pending');
            $table->string('applied_date')->nullable();

            $table->timestamps();

            $table->unique(['scholarship_id', 'alumni_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};
