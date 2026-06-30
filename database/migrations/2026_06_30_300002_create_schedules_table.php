<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('week_start'); // always a Monday
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
