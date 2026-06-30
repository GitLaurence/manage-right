<?php

use App\Livewire\Business\BranchManager;
use App\Livewire\Business\InviteEmployee;
use App\Livewire\Onboarding\BusinessSetup;
use App\Livewire\Attendance\ClockIn;
use App\Livewire\Attendance\ManagerReview;
use App\Livewire\Scheduling\ShiftTemplates;
use App\Livewire\Scheduling\WeeklySchedule;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/onboarding', BusinessSetup::class)->name('onboarding');

    Route::middleware(['has.business'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::livewire('/branches', BranchManager::class)->name('branches.index');
        Route::livewire('/employees/invite', InviteEmployee::class)->name('employees.invite');
        Route::livewire('/shift-templates', ShiftTemplates::class)->name('shift-templates.index');
        Route::livewire('/schedule', WeeklySchedule::class)->name('schedule.index');
        Route::livewire('/attendance/clock', ClockIn::class)->name('attendance.clock');
        Route::livewire('/attendance/review', ManagerReview::class)->name('attendance.review');
    });
});

Route::get('/invitations/{token}', function (string $token) {
    $invitation = \App\Models\Invitation::where('token', $token)
        ->whereNull('accepted_at')
        ->where('expires_at', '>', now())
        ->firstOrFail();

    // Store token in session so it can be consumed after login/register
    session(['invitation_token' => $token]);

    return auth()->check()
        ? redirect()->route('dashboard') // TODO: Phase 3 — auto-accept on login
        : redirect()->route('register');
})->name('invitations.accept');

require __DIR__.'/settings.php';
