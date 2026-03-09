<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {


            if (!Schema::hasColumn('support_tickets', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()
                      ->constrained('users')->nullOnDelete()
                      ->after('priority');
            }


            if (!Schema::hasColumn('support_tickets', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('admin_id');
            }


            if (!Schema::hasColumn('support_tickets', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('admin_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('support_tickets', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }
            if (Schema::hasColumn('support_tickets', 'admin_reply')) {
                $table->dropColumn('admin_reply');
            }
            if (Schema::hasColumn('support_tickets', 'admin_id')) {
                $table->dropConstrainedForeignId('admin_id');
            }
        });
    }
};
