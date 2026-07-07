<?php

use App\Livewire\Reports\AttendanceSummary;
use App\Models\AttendanceLog;
use Livewire\Livewire;

test('summary counts reflect today logs for the business', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employeeA = addMemberToBusiness($business, $branch, 'employee');
    $employeeB = addMemberToBusiness($business, $branch, 'employee');

    AttendanceLog::factory()->create([
        'user_id' => $employeeA->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'type' => 'time_in',
        'logged_at' => now(),
    ]);

    AttendanceLog::factory()->approved()->create([
        'user_id' => $employeeB->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'type' => 'time_in',
        'logged_at' => now(),
    ]);

    $this->actingAs($owner);

    $component = Livewire::test(AttendanceSummary::class)->set('branchId', $branch->id);

    expect($component->instance()->summary)
        ->total_in->toBe(2)
        ->pending_count->toBe(1)
        ->approved_count->toBe(1);
});

test('employee cannot access attendance summary', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee)
        ->get(route('reports.attendance'))
        ->assertForbidden();
});
