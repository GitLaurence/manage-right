<?php

use App\Livewire\Reports\PayrollSummary;
use App\Models\AttendanceLog;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('payroll rows aggregate days present for the week', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $weekStart = Carbon::now()->startOfWeek();

    AttendanceLog::factory()->create([
        'user_id' => $employee->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'type' => 'time_in',
        'logged_at' => $weekStart->copy()->addHours(9),
    ]);

    $this->actingAs($owner);

    $component = Livewire::test(PayrollSummary::class)
        ->set('branchId', $branch->id)
        ->set('weekStart', $weekStart->toDateString());

    $rows = $component->instance()->rows;

    expect($rows)->toHaveCount(1);
    expect($rows->first()->days_present)->toBe(1);
});

test('payroll summary can be exported as csv', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    AttendanceLog::factory()->create([
        'user_id' => $employee->id,
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'type' => 'time_in',
        'logged_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(PayrollSummary::class)
        ->set('branchId', $branch->id)
        ->call('exportCsv')
        ->assertFileDownloaded();
});

test('employee cannot access payroll summary', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee)
        ->get(route('reports.payroll'))
        ->assertForbidden();
});
