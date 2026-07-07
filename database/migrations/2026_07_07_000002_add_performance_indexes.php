<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index(['business_id', 'logged_at']);
            $table->index(['branch_id', 'logged_at']);
            $table->index(['business_id', 'status']);
        });

        Schema::table('employee_requests', function (Blueprint $table) {
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'from_date', 'to_date']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->index('business_id');
        });

        Schema::table('business_users', function (Blueprint $table) {
            $table->index(['business_id', 'role']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->index('business_id');
        });

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'logged_at']);
            $table->dropIndex(['branch_id', 'logged_at']);
            $table->dropIndex(['business_id', 'status']);
        });

        Schema::table('employee_requests', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'status']);
            $table->dropIndex(['business_id', 'from_date', 'to_date']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
        });

        Schema::table('business_users', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'role']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
        });

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
        });
    }
};
