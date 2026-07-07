<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EmployeeRequest>
 */
class EmployeeRequestFactory extends Factory
{
    public function definition(): array
    {
        $branch = Branch::factory()->create();

        return [
            'user_id' => User::factory(),
            'business_id' => $branch->business_id,
            'branch_id' => $branch->id,
            'type' => 'leave',
            'leave_type' => 'vacation',
            'from_date' => today()->toDateString(),
            'to_date' => today()->toDateString(),
            'reason' => fake()->sentence(),
            'status' => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }
}
