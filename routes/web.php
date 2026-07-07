<?php

use App\Livewire\Business\BranchManager;
use App\Livewire\Business\InviteEmployee;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Onboarding\BusinessSetup;
use App\Livewire\Attendance\ClockIn;
use App\Livewire\Attendance\ManagerReview;
use App\Livewire\Requests\MyRequests;
use App\Livewire\Requests\ManagerApprovals;
use App\Livewire\Reports\AttendanceSummary;
use App\Livewire\Reports\PayrollSummary;
use App\Livewire\Scheduling\ShiftTemplates;
use App\Livewire\Scheduling\WeeklySchedule;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/onboarding', BusinessSetup::class)->name('onboarding');

    Route::middleware(['has.business'])->group(function () {
        Route::livewire('/dashboard', Overview::class)->name('dashboard');
        Route::livewire('/branches', BranchManager::class)->name('branches.index');
        Route::livewire('/employees/invite', InviteEmployee::class)->name('employees.invite');
        Route::livewire('/shift-templates', ShiftTemplates::class)->name('shift-templates.index');
        Route::livewire('/schedule', WeeklySchedule::class)->name('schedule.index');
        Route::livewire('/attendance/clock', ClockIn::class)->name('attendance.clock');
        Route::livewire('/attendance/review', ManagerReview::class)->name('attendance.review');

        // Phase 5 — Employee Requests
        Route::livewire('/requests', MyRequests::class)->name('requests.index');
        Route::livewire('/requests/approvals', ManagerApprovals::class)->name('requests.approvals');

        // Phase 6 — Reports
        Route::livewire('/reports/attendance', AttendanceSummary::class)->name('reports.attendance');
        Route::livewire('/reports/payroll', PayrollSummary::class)->name('reports.payroll');
    });
});

Route::get('/invitations/{token}', function (string $token) {
    $invitation = \App\Models\Invitation::withoutTenant()
        ->where('token', $token)
        ->whereNull('accepted_at')
        ->where('expires_at', '>', now())
        ->firstOrFail();

    if (auth()->check()) {
        app(\App\Actions\AcceptInvitation::class)(auth()->user(), $invitation);

        return redirect()->route('dashboard');
    }

    session(['invitation_token' => $token]);

    return redirect()->route('register');
})->name('invitations.accept');

require __DIR__.'/settings.php';
