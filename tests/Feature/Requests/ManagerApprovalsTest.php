<?php

use App\Livewire\Requests\ManagerApprovals;
use App\Models\EmployeeRequest;
use Livewire\Livewire;

test('manager can approve a request', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $request = EmployeeRequest::factory()->create([
        'user_id' => $employee->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManagerApprovals::class)
        ->set('branchId', $branch->id)
        ->call('approve', $request->id);

    expect($request->fresh()->status)->toBe('approved');
});

test('manager can reject a request with remarks', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $request = EmployeeRequest::factory()->create([
        'user_id' => $employee->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManagerApprovals::class)
        ->set('branchId', $branch->id)
        ->call('openReject', $request->id)
        ->set('remarks', 'Not enough notice')
        ->call('reject');

    expect($request->fresh())
        ->status->toBe('rejected')
        ->manager_remarks->toBe('Not enough notice');
});

test('employee cannot access manager approvals', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee)
        ->get(route('requests.approvals'))
        ->assertForbidden();
});
