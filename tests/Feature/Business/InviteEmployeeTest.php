<?php

use App\Livewire\Business\InviteEmployee;
use App\Models\Invitation;
use Livewire\Livewire;

test('owner can create an invitation', function () {
    ['user' => $owner, 'branch' => $branch] = createBusinessWithOwner();

    $this->actingAs($owner);

    Livewire::test(InviteEmployee::class)
        ->set('role', 'employee')
        ->set('branchId', $branch->id)
        ->call('send');

    expect(Invitation::where('branch_id', $branch->id)->where('role', 'employee')->exists())->toBeTrue();
});

test('owner can cancel a pending invitation', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();

    $invitation = Invitation::factory()->create([
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'invited_by' => $owner->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(InviteEmployee::class)->call('cancel', $invitation->id);

    expect(Invitation::find($invitation->id))->toBeNull();
});

test('manager cannot access invite employee', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $manager = addMemberToBusiness($business, $branch, 'manager');

    $this->actingAs($manager)
        ->get(route('employees.invite'))
        ->assertForbidden();
});
