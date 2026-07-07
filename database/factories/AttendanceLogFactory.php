<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AttendanceLog>
 */
class AttendanceLogFactory extends Factory
{
    public function definition(): array
    {
        $branch = Branch::factory()->create();

        return [
            'user_id' => User::factory(),
            'business_id' => $branch->business_id,
            'branch_id' => $branch->id,
            'type' => 'time_in',
            'logged_at' => now(),
            'status' => 'pending',
        ];
    }

    public function timeOut(): static
    {
        return $this->state(['type' => 'time_out']);
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }
}
