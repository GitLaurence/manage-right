<?php

use App\Livewire\Attendance\ManagerReview;
use App\Models\AttendanceLog;
use Livewire\Livewire;

test('manager can approve an attendance log', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $log = AttendanceLog::factory()->create([
        'user_id' => $employee->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'logged_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(ManagerReview::class)
        ->set('branchId', $branch->id)
        ->call('approve', $log->id);

    expect($log->fresh()->status)->toBe('approved');
});

test('employee cannot access manager review', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee)
        ->get(route('attendance.review'))
        ->assertForbidden();
});
