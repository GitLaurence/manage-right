<?php

use App\Models\Branch;
use App\Models\ShiftTemplate;

test('tenant scope hides other businesses data even without an explicit business_id filter', function () {
    ['business' => $businessA] = createBusinessWithOwner();
    ['user' => $ownerB, 'business' => $businessB] = createBusinessWithOwner();

    ShiftTemplate::factory()->create(['business_id' => $businessA->id, 'name' => 'A Template']);
    ShiftTemplate::factory()->create(['business_id' => $businessB->id, 'name' => 'B Template']);

    $this->actingAs($ownerB);

    $visible = ShiftTemplate::all();

    expect($visible)->toHaveCount(1);
    expect($visible->first()->name)->toBe('B Template');
});

test('tenant scope does not affect queries when there is no authenticated user', function () {
    ['business' => $businessA] = createBusinessWithOwner();
    ['business' => $businessB] = createBusinessWithOwner();

    Branch::factory()->create(['business_id' => $businessA->id]);
    Branch::factory()->create(['business_id' => $businessB->id]);

    expect(Branch::count())->toBeGreaterThanOrEqual(4);
});

test('withoutTenant escape hatch bypasses the scope', function () {
    ['user' => $ownerA, 'business' => $businessA] = createBusinessWithOwner();
    ['business' => $businessB] = createBusinessWithOwner();

    ShiftTemplate::factory()->create(['business_id' => $businessA->id]);
    ShiftTemplate::factory()->create(['business_id' => $businessB->id]);

    $this->actingAs($ownerA);

    expect(ShiftTemplate::withoutTenant()->count())->toBe(2);
});
