<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some exported databases may be imported as MyISAM, which silently drops
     * real foreign-key enforcement. This migration repairs that safely.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->convertExistingTablesToInnoDB();

        foreach ($this->foreignKeys() as $foreignKey) {
            $this->addForeignKeyIfSafe(...$foreignKey);
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach (array_reverse($this->foreignKeys()) as [$table, $column]) {
            $name = $this->foreignKeyName($table, $column);

            if (Schema::hasTable($table) && $this->constraintExists($table, $name)) {
                DB::statement(sprintf(
                    'ALTER TABLE %s DROP FOREIGN KEY %s',
                    $this->quoteIdentifier($table),
                    $this->quoteIdentifier($name)
                ));
            }
        }
    }

    private function convertExistingTablesToInnoDB(): void
    {
        $tables = DB::table('information_schema.tables')
            ->selectRaw('TABLE_NAME as table_name')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_type', 'BASE TABLE')
            ->where('engine', '!=', 'InnoDB')
            ->pluck('table_name');

        foreach ($tables as $table) {
            DB::statement(sprintf('ALTER TABLE %s ENGINE=InnoDB', $this->quoteIdentifier($table)));
        }
    }

    private function addForeignKeyIfSafe(
        string $table,
        string $column,
        string $referencesTable,
        string $referencesColumn,
        string $onDelete
    ): void {
        $name = $this->foreignKeyName($table, $column);

        if (
            !Schema::hasTable($table) ||
            !Schema::hasTable($referencesTable) ||
            !Schema::hasColumn($table, $column) ||
            !Schema::hasColumn($referencesTable, $referencesColumn) ||
            $this->constraintExists($table, $name) ||
            $this->hasOrphanedRows($table, $column, $referencesTable, $referencesColumn)
        ) {
            return;
        }

        if ($onDelete === 'SET NULL' && !$this->columnIsNullable($table, $column)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE %s ADD CONSTRAINT %s FOREIGN KEY (%s) REFERENCES %s (%s) ON DELETE %s',
            $this->quoteIdentifier($table),
            $this->quoteIdentifier($name),
            $this->quoteIdentifier($column),
            $this->quoteIdentifier($referencesTable),
            $this->quoteIdentifier($referencesColumn),
            $onDelete
        ));
    }

    private function hasOrphanedRows(
        string $table,
        string $column,
        string $referencesTable,
        string $referencesColumn
    ): bool {
        $orphans = DB::table($table)
            ->leftJoin($referencesTable, "{$table}.{$column}", '=', "{$referencesTable}.{$referencesColumn}")
            ->whereNotNull("{$table}.{$column}")
            ->whereNull("{$referencesTable}.{$referencesColumn}")
            ->limit(1)
            ->count();

        return $orphans > 0;
    }

    private function constraintExists(string $table, string $constraintName): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $constraintName)
            ->exists();
    }

    private function columnIsNullable(string $table, string $column): bool
    {
        return DB::table('information_schema.columns')
            ->selectRaw('IS_NULLABLE as is_nullable')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->value('is_nullable') === 'YES';
    }

    private function foreignKeyName(string $table, string $column): string
    {
        return "{$table}_{$column}_foreign";
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * @return array<int, array{string, string, string, string, string}>
     */
    private function foreignKeys(): array
    {
        return [
            ['admin_notifications', 'company_user_id', 'users', 'id', 'SET NULL'],
            ['admin_notifications', 'company_profile_id', 'company_profiles', 'id', 'SET NULL'],
            ['alumni_profiles', 'user_id', 'users', 'id', 'CASCADE'],
            ['announcements', 'created_by', 'users', 'id', 'SET NULL'],
            ['company_profiles', 'user_id', 'users', 'id', 'CASCADE'],
            ['company_profiles', 'approved_by', 'users', 'id', 'SET NULL'],
            ['jobs', 'company_user_id', 'users', 'id', 'SET NULL'],
            ['jobs', 'organizer_user_id', 'users', 'id', 'SET NULL'],
            ['jobs', 'approved_by', 'users', 'id', 'SET NULL'],
            ['job_applications', 'job_id', 'jobs', 'id', 'CASCADE'],
            ['job_applications', 'alumni_user_id', 'users', 'id', 'CASCADE'],
            ['leaderboard_entries', 'alumni_user_id', 'users', 'id', 'CASCADE'],
            ['recommendations', 'from_user_id', 'users', 'id', 'SET NULL'],
            ['recommendations', 'to_user_id', 'users', 'id', 'SET NULL'],
            ['saved_jobs', 'job_id', 'jobs', 'id', 'CASCADE'],
            ['saved_jobs', 'alumni_user_id', 'users', 'id', 'CASCADE'],
            ['scholarships', 'created_by_user_id', 'users', 'id', 'SET NULL'],
            ['scholarship_applications', 'scholarship_id', 'scholarships', 'id', 'CASCADE'],
            ['scholarship_applications', 'alumni_user_id', 'users', 'id', 'CASCADE'],
            ['success_stories', 'alumni_user_id', 'users', 'id', 'SET NULL'],
            ['success_stories', 'created_by', 'users', 'id', 'SET NULL'],
            ['support_tickets', 'user_id', 'users', 'id', 'SET NULL'],
            ['support_tickets', 'admin_id', 'users', 'id', 'SET NULL'],
            ['workshops', 'organizer_user_id', 'users', 'id', 'SET NULL'],
            ['workshops', 'company_user_id', 'users', 'id', 'SET NULL'],
            ['workshop_registrations', 'workshop_id', 'workshops', 'id', 'CASCADE'],
            ['workshop_registrations', 'alumni_user_id', 'users', 'id', 'CASCADE'],
        ];
    }
};
