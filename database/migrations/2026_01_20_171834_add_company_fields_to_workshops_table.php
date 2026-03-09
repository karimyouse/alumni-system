<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (!Schema::hasColumn('workshops', 'company_user_id')) {
                $table->foreignId('company_user_id')->nullable()->constrained('users')->nullOnDelete();
            }


            if (!Schema::hasColumn('workshops', 'proposal_status')) {
                $table->string('proposal_status')->default('approved');
            }
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'company_user_id')) {
                $table->dropConstrainedForeignId('company_user_id');
            }
            if (Schema::hasColumn('workshops', 'proposal_status')) {
                $table->dropColumn('proposal_status');
            }
        });
    }
};
