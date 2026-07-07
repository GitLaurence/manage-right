<?php

use App\Livewire\Requests\MyRequests;
use App\Models\EmployeeRequest;
use Livewire\Livewire;

test('employee can submit a leave request', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee);

    Livewire::test(MyRequests::class)
        ->set('type', 'leave')
        ->set('leaveType', 'sick')
        ->set('fromDate', today()->toDateString())
        ->set('toDate', today()->toDateString())
        ->set('reason', 'Not feeling well')
        ->call('submit');

    expect(EmployeeRequest::where('user_id', $employee->id)->where('type', 'leave')->exists())->toBeTrue();
});

test('employee only sees their own requests', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employeeA = addMemberToBusiness($business, $branch, 'employee');
    $employeeB = addMemberToBusiness($business, $branch, 'employee');

    EmployeeRequest::factory()->create([
        'user_id' => $employeeB->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
    ]);

    $this->actingAs($employeeA);

    $component = Livewire::test(MyRequests::class);

    expect($component->instance()->requests)->toBeEmpty();
});
