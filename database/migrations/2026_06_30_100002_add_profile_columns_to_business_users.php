<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_users', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contractual'])->nullable();
            $table->enum('rate_type', ['hourly', 'daily', 'monthly'])->nullable();
            $table->decimal('rate', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('business_users', function (Blueprint $table) {
            $table->dropColumn(['position', 'employment_type', 'rate_type', 'rate']);
        });
    }
};
