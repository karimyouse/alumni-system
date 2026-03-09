<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('support_tickets')) return;


        if (!Schema::hasColumn('support_tickets', 'role')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->string('role', 50)->nullable()->after('email');
            });
        }


        if (!Schema::hasColumn('support_tickets', 'identifier')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->string('identifier', 255)->nullable()->after('role');
            });
        }


        if (!Schema::hasColumn('support_tickets', 'title')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->string('title', 255)->nullable()->after('identifier');
            });
        }


        if (!Schema::hasColumn('support_tickets', 'admin_id')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->foreignId('admin_id')->nullable()
                    ->constrained('users')->nullOnDelete()
                    ->after('priority');
            });
        }


        if (!Schema::hasColumn('support_tickets', 'admin_reply')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->text('admin_reply')->nullable()->after('admin_id');
            });
        }

        
        if (!Schema::hasColumn('support_tickets', 'resolved_at')) {
            Schema::table('support_tickets', function (Blueprint $table) {
                $table->timestamp('resolved_at')->nullable()->after('admin_reply');
            });
        }
    }

    public function down(): void
    {

    }
};
