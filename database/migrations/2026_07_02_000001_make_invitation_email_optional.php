<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'email']);
        });

        DB::statement('ALTER TABLE invitations ALTER COLUMN email DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE invitations ALTER COLUMN email SET NOT NULL');

        Schema::table('invitations', function (Blueprint $table) {
            $table->unique(['business_id', 'email']);
        });
    }
};
