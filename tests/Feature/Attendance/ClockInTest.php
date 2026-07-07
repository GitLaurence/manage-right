<?php

use App\Livewire\Attendance\ClockIn;
use App\Models\AttendanceLog;
use Livewire\Livewire;

test('guests are redirected to login', function () {
    $this->get(route('attendance.clock'))->assertRedirect(route('login'));
});

test('employee can clock in then clock out', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee);

    Livewire::test(ClockIn::class)->call('record');

    expect(AttendanceLog::where('user_id', $employee->id)->where('type', 'time_in')->count())->toBe(1);

    Livewire::test(ClockIn::class)->call('record');

    expect(AttendanceLog::where('user_id', $employee->id)->where('type', 'time_out')->count())->toBe(1);
});

test('clocking in does not create duplicate logs when already done for the day', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee);

    Livewire::test(ClockIn::class)->call('record');
    Livewire::test(ClockIn::class)->call('record');
    Livewire::test(ClockIn::class)->call('record');

    expect(AttendanceLog::where('user_id', $employee->id)->count())->toBe(2);
});
