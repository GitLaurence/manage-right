<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE schedules
            SET business_id = branches.business_id
            FROM branches
            WHERE schedules.branch_id = branches.id
        ');

        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable(false)->change();
        });

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE schedule_entries
            SET business_id = schedules.business_id
            FROM schedules
            WHERE schedule_entries.schedule_id = schedules.id
        ');

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('business_id');
        });
    }
};
