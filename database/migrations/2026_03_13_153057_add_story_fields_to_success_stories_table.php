<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('success_stories')) {
            return;
        }

        $addName = !Schema::hasColumn('success_stories', 'name');
        $addGraduationYear = !Schema::hasColumn('success_stories', 'graduation_year');
        $addCurrentPosition = !Schema::hasColumn('success_stories', 'current_position');

        if (!$addName && !$addGraduationYear && !$addCurrentPosition) {
            return;
        }

        Schema::table('success_stories', function (Blueprint $table) use ($addName, $addGraduationYear, $addCurrentPosition) {
            if ($addName) {
                $table->string('name')->nullable()->after('body');
            }

            if ($addGraduationYear) {
                $table->string('graduation_year', 50)->nullable()->after('name');
            }

            if ($addCurrentPosition) {
                $table->string('current_position')->nullable()->after('graduation_year');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('success_stories')) {
            return;
        }

        $dropColumns = [];

        if (Schema::hasColumn('success_stories', 'current_position')) {
            $dropColumns[] = 'current_position';
        }

        if (Schema::hasColumn('success_stories', 'graduation_year')) {
            $dropColumns[] = 'graduation_year';
        }

        if (Schema::hasColumn('success_stories', 'name')) {
            $dropColumns[] = 'name';
        }

        if (!empty($dropColumns)) {
            Schema::table('success_stories', function (Blueprint $table) use ($dropColumns) {
                $table->dropColumn($dropColumns);
            });
        }
    }
};
