<?php

use App\Livewire\Business\BranchManager;
use App\Models\Branch;
use Livewire\Livewire;

test('owner can create a branch', function () {
    ['user' => $owner] = createBusinessWithOwner();

    $this->actingAs($owner);

    Livewire::test(BranchManager::class)
        ->set('branchName', 'Downtown')
        ->set('timezone', 'Asia/Manila')
        ->call('save');

    expect(Branch::where('name', 'Downtown')->exists())->toBeTrue();
});

test('owner can edit a branch', function () {
    ['user' => $owner, 'branch' => $branch] = createBusinessWithOwner();

    $this->actingAs($owner);

    Livewire::test(BranchManager::class)
        ->call('openEdit', $branch)
        ->set('branchName', 'Renamed Branch')
        ->call('save');

    expect($branch->fresh()->name)->toBe('Renamed Branch');
});

test('manager cannot access branch management', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $manager = addMemberToBusiness($business, $branch, 'manager');

    $this->actingAs($manager)
        ->get(route('branches.index'))
        ->assertForbidden();
});
