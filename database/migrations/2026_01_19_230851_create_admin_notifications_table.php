<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('action_url')->nullable();

            $table->foreignId('company_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_profile_id')->nullable()->constrained('company_profiles')->nullOnDelete();

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
