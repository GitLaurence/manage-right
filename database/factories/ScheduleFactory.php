<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $branch = Branch::factory()->create();

        return [
            'business_id' => $branch->business_id,
            'branch_id' => $branch->id,
            'week_start' => Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString(),
            'published_at' => null,
        ];
    }
}
