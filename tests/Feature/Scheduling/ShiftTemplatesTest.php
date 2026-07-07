<?php

use App\Livewire\Scheduling\ShiftTemplates;
use App\Models\ShiftTemplate;
use Livewire\Livewire;

test('manager can create a shift template', function () {
    ['user' => $owner] = createBusinessWithOwner();

    $this->actingAs($owner);

    Livewire::test(ShiftTemplates::class)
        ->set('name', 'Night Shift')
        ->set('startTime', '20:00')
        ->set('endTime', '04:00')
        ->set('breakMinutes', 30)
        ->set('color', 'purple')
        ->call('save');

    expect(ShiftTemplate::where('name', 'Night Shift')->exists())->toBeTrue();
});

test('employee cannot access shift templates', function () {
    ['user' => $owner, 'business' => $business, 'branch' => $branch] = createBusinessWithOwner();
    $employee = addMemberToBusiness($business, $branch, 'employee');

    $this->actingAs($employee)
        ->get(route('shift-templates.index'))
        ->assertForbidden();
});
