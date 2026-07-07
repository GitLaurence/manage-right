<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Creates a Business with an owner User, a Branch, and a BusinessUser
 * membership (role: owner), and sets the owner's current_business_id.
 *
 * @return array{user: \App\Models\User, business: \App\Models\Business, branch: \App\Models\Branch}
 */
function createBusinessWithOwner(array $userAttributes = []): array
{
    $user = \App\Models\User::factory()->create($userAttributes);
    $business = \App\Models\Business::factory()->create(['owner_id' => $user->id]);
    $branch = \App\Models\Branch::factory()->create(['business_id' => $business->id]);

    \App\Models\BusinessUser::factory()->owner()->create([
        'user_id' => $user->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
    ]);

    $user->update(['current_business_id' => $business->id]);

    return ['user' => $user, 'business' => $business, 'branch' => $branch];
}

/**
 * Adds a new User as a member of an existing business/branch with the given
 * role, and sets their current_business_id so they're "acting" in that
 * business for tenant-scoped queries.
 */
function addMemberToBusiness(\App\Models\Business $business, \App\Models\Branch $branch, string $role = 'employee', array $userAttributes = []): \App\Models\User
{
    $user = \App\Models\User::factory()->create($userAttributes);

    \App\Models\BusinessUser::factory()->create([
        'user_id' => $user->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'role' => $role,
    ]);

    $user->update(['current_business_id' => $business->id]);

    return $user;
}
