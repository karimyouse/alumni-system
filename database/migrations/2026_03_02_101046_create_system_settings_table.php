<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            $table->string('institution_name')->default('Palestine Technical College');
            $table->string('primary_color')->default('#2563eb'); 

            $table->boolean('email_new_user_notifications')->default(true);
            $table->boolean('email_content_approval_alerts')->default(true);
            $table->boolean('email_weekly_reports')->default(false);

            $table->boolean('auto_backup')->default(true);
            $table->timestamp('last_backup_at')->nullable();

            $table->boolean('require_2fa')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
