<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {


            if (!Schema::hasColumn('support_tickets', 'tracking_code')) {
                $table->string('tracking_code', 30)->nullable()->unique()->after('id');
            }

            
            if (!Schema::hasColumn('support_tickets', 'admin_replied_at')) {
                $table->timestamp('admin_replied_at')->nullable()->after('admin_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {

            if (Schema::hasColumn('support_tickets', 'admin_replied_at')) {
                $table->dropColumn('admin_replied_at');
            }

            if (Schema::hasColumn('support_tickets', 'tracking_code')) {
                $table->dropUnique(['tracking_code']);
                $table->dropColumn('tracking_code');
            }
        });
    }
};
