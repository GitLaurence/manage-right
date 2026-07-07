<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'branch_id' => null,
            'invited_by' => User::factory(),
            'email' => fake()->unique()->safeEmail(),
            'position' => null,
            'role' => 'employee',
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ];
    }
}
