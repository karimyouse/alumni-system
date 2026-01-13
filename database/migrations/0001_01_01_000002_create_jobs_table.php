<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();


            $table->foreignId('company_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('title');
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->string('type')->nullable();
            $table->string('salary')->nullable();
            $table->string('posted')->nullable();
            $table->text('description')->nullable();

            $table->string('status')->default('active'); 
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
