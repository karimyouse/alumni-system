<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        if (!Schema::hasTable('company_profiles')) {
            Schema::create('company_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('company_name');
                $table->string('contact_person_name');
                $table->string('industry')->nullable();
                $table->string('location')->nullable();
                $table->string('website')->nullable();
                $table->text('description')->nullable();

                $table->string('status')->default('pending'); 
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('admin_note')->nullable();

                $table->timestamps();
            });

            return;
        }

        Schema::table('company_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('company_profiles', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('company_profiles', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('company_profiles', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('company_profiles', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }
            if (!Schema::hasColumn('company_profiles', 'admin_note')) {
                $table->text('admin_note')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('company_profiles')) return;

        Schema::table('company_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('company_profiles', 'admin_note')) $table->dropColumn('admin_note');
            if (Schema::hasColumn('company_profiles', 'approved_by')) $table->dropColumn('approved_by');
            if (Schema::hasColumn('company_profiles', 'rejected_at')) $table->dropColumn('rejected_at');
            if (Schema::hasColumn('company_profiles', 'approved_at')) $table->dropColumn('approved_at');
            if (Schema::hasColumn('company_profiles', 'status')) $table->dropColumn('status');
        });
    }
};
