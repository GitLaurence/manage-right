<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Supabase Security Advisor flags tables without RLS enabled.
     * Our Laravel app connects as the postgres superuser which bypasses RLS,
     * so enabling it here blocks direct PostgREST/API access without affecting the app.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'migrations',
            'users',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'passkeys',
            'businesses',
            'branches',
            'business_users',
            'invitations',
            'shift_templates',
            'schedules',
            'schedule_entries',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"{$table}\" ENABLE ROW LEVEL SECURITY");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $tables = [
            'users',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'passkeys',
            'businesses',
            'branches',
            'business_users',
            'invitations',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"{$table}\" DISABLE ROW LEVEL SECURITY");
        }
    }
};
