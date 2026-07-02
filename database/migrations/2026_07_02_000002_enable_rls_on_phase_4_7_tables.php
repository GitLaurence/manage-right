<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Same rationale as 2026_06_30_200001_enable_rls_on_all_tables — the Phase 4-7
     * migrations that added these tables never enabled RLS. Our Laravel app connects
     * as the postgres superuser which bypasses RLS, so this only blocks direct
     * PostgREST/API access without affecting the app.
     */
    public function up(): void
    {
        $tables = ['attendance_logs', 'employee_requests', 'activity_logs'];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"{$table}\" ENABLE ROW LEVEL SECURITY");
        }
    }

    public function down(): void
    {
        $tables = ['attendance_logs', 'employee_requests', 'activity_logs'];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"{$table}\" DISABLE ROW LEVEL SECURITY");
        }
    }
};
