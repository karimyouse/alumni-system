<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            // who created ticket (optional)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // if created without logged user
            $table->string('name')->nullable();
            $table->string('email')->nullable();

            $table->string('title');
            $table->text('message')->nullable();

            $table->string('status')->default('open'); // open | in_progress | resolved
            $table->string('priority')->default('medium'); // low | medium | high

            // admin handling
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_reply')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
