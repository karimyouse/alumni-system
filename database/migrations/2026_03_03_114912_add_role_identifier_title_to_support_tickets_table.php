<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {

            // ✅ Add missing fields safely (only if not exists)
            if (!Schema::hasColumn('support_tickets', 'role')) {
                $table->string('role', 50)->nullable()->after('email');
            }

            if (!Schema::hasColumn('support_tickets', 'identifier')) {
                $table->string('identifier', 255)->nullable()->after('role');
            }

            if (!Schema::hasColumn('support_tickets', 'title')) {
                $table->string('title', 255)->nullable()->after('identifier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('support_tickets', 'identifier')) {
                $table->dropColumn('identifier');
            }
            if (Schema::hasColumn('support_tickets', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
