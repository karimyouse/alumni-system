<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('jobs', 'organizer_user_id')) {
                $table->unsignedBigInteger('organizer_user_id')->nullable()->after('company_user_id');
            }

            if (!Schema::hasColumn('jobs', 'organizer_role')) {
                $table->string('organizer_role', 20)->nullable()->after('organizer_user_id');
            }
        });

        Schema::table('jobs', function (Blueprint $table) {
            // مهم: حذف القيد أولاً
            try {
                $table->dropForeign(['company_user_id']);
            } catch (\Throwable $e) {
            }
        });

        Schema::table('jobs', function (Blueprint $table) {
            // جعل company_user_id nullable
            $table->foreignId('company_user_id')->nullable()->change();
        });

        Schema::table('jobs', function (Blueprint $table) {
            try {
                $table->foreign('company_user_id')->references('id')->on('users')->nullOnDelete();
            } catch (\Throwable $e) {
            }
        });

        // تعبئة البيانات القديمة
        DB::table('jobs')
            ->whereNotNull('company_user_id')
            ->update([
                'organizer_user_id' => DB::raw('company_user_id'),
                'organizer_role' => 'company',
            ]);
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            try {
                $table->dropForeign(['company_user_id']);
            } catch (\Throwable $e) {
            }
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('company_user_id')->nullable(false)->change();
        });

        Schema::table('jobs', function (Blueprint $table) {
            try {
                $table->foreign('company_user_id')->references('id')->on('users')->cascadeOnDelete();
            } catch (\Throwable $e) {
            }

            if (Schema::hasColumn('jobs', 'organizer_role')) {
                $table->dropColumn('organizer_role');
            }

            if (Schema::hasColumn('jobs', 'organizer_user_id')) {
                $table->dropColumn('organizer_user_id');
            }
        });
    }
};
