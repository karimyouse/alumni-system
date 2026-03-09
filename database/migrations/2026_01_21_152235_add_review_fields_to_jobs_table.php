<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {

            if (!Schema::hasColumn('jobs', 'approval_status')) {
                $table->string('approval_status')->default('approved');
                
            }

            if (!Schema::hasColumn('jobs', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }

            if (!Schema::hasColumn('jobs', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }

            if (!Schema::hasColumn('jobs', 'reject_reason')) {
                $table->text('reject_reason')->nullable();
            }

            if (!Schema::hasColumn('jobs', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (Schema::hasColumn('jobs', 'is_featured')) $table->dropColumn('is_featured');
            if (Schema::hasColumn('jobs', 'reject_reason')) $table->dropColumn('reject_reason');
            if (Schema::hasColumn('jobs', 'approved_by')) $table->dropColumn('approved_by');
            if (Schema::hasColumn('jobs', 'approved_at')) $table->dropColumn('approved_at');
            if (Schema::hasColumn('jobs', 'approval_status')) $table->dropColumn('approval_status');
        });
    }
};
