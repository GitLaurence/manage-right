<?php

use App\Livewire\Scheduling\WeeklySchedule;
use App\Models\ScheduleEntry;
use App\Models\ShiftTemplate;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('manager can assign a shift via drag-and-drop', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');
    $template = ShiftTemplate::factory()->create(['business_id' => $business->id]);

    $date = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

    $this->actingAs($owner);

    Livewire::test(WeeklySchedule::class)
        ->set('branchId', $branch->id)
        ->call('assign', $template->id, $employee->id, $date);

    expect(ScheduleEntry::where('user_id', $employee->id)->whereDate('date', $date)->exists())->toBeTrue();
});

test('manager can assign a shift via the tap-to-assign picker', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');
    $template = ShiftTemplate::factory()->create(['business_id' => $business->id]);

    $date = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

    $this->actingAs($owner);

    Livewire::test(WeeklySchedule::class)
        ->set('branchId', $branch->id)
        ->call('openAssign', $employee->id, $date)
        ->call('assignFromModal', $template->id);

    expect(ScheduleEntry::where('user_id', $employee->id)->whereDate('date', $date)->exists())->toBeTrue();
});

test('employee cannot access the weekly schedule builder', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee)
        ->get(route('schedule.index'))
        ->assertForbidden();
});
