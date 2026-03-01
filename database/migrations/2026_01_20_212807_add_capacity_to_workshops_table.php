<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (!Schema::hasColumn('workshops', 'capacity')) {
                $table->unsignedInteger('capacity')->nullable(); // null = unlimited
            }
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            if (Schema::hasColumn('workshops', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
};
