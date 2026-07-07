<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    public function definition(): array
    {
        $branch = Branch::factory()->create();

        return [
            'user_id' => User::factory(),
            'business_id' => $branch->business_id,
            'branch_id' => $branch->id,
            'action' => 'test.action',
        ];
    }
}
