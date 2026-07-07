<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\BusinessUser>
 */
class BusinessUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'business_id' => Business::factory(),
            'branch_id' => null,
            'role' => 'employee',
        ];
    }

    public function owner(): static
    {
        return $this->state(['role' => 'owner']);
    }

    public function manager(): static
    {
        return $this->state(['role' => 'manager']);
    }

    public function employee(): static
    {
        return $this->state(['role' => 'employee']);
    }
}
