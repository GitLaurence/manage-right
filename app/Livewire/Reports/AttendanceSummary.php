<?php

namespace App\Livewire\Reports;

use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Attendance Summary')]
class AttendanceSummary extends Component
{
    public string $date;
    public ?int $branchId = null;

    public function mount(): void
    {
        $business = Auth::user()->currentBusiness;

        abort_unless($business && Auth::user()->isManagerOrOwnerOf($business), 403);

        $this->date     = today()->toDateString();
        $this->branchId = $business->branches()->value('id');
    }

    #[Computed]
    public function business()
    {
        return Auth::user()->currentBusiness;
    }

    #[Computed]
    public function branches()
    {
        return $this->business->branches()->orderBy('name')->get();
    }

    #[Computed]
    public function logs()
    {
        if ($this->branches->isEmpty()) {
            return collect();
        }

        return AttendanceLog::with(['user', 'branch', 'scheduleEntry'])
            ->where('business_id', $this->business->id)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereDate('logged_at', $this->date)
            ->orderBy('logged_at')
            ->get();
    }

    #[Computed]
    public function summary(): array
    {
        $logs = $this->logs;

        $timesIn  = $logs->where('type', 'time_in');
        $timesOut = $logs->where('type', 'time_out');

        return [
            'total_in'         => $timesIn->count(),
            'total_out'        => $timesOut->count(),
            'late_count'       => $timesIn->filter(fn ($l) => $l->isLate())->count(),
            'undertime_count'  => $timesOut->filter(fn ($l) => $l->hasUndertime())->count(),
            'overtime_count'   => $timesOut->filter(fn ($l) => $l->hasOvertime())->count(),
            'pending_count'    => $logs->where('status', 'pending')->count(),
            'approved_count'   => $logs->where('status', 'approved')->count(),
            'flagged_count'    => $logs->where('status', 'flagged')->count(),
        ];
    }

    #[Computed]
    public function employeeRows(): \Illuminate\Support\Collection
    {
        return $this->logs
            ->groupBy('user_id')
            ->map(function ($entries) {
                $timeIn  = $entries->where('type', 'time_in')->sortBy('logged_at')->first();
                $timeOut = $entries->where('type', 'time_out')->sortByDesc('logged_at')->first();

                return (object) [
                    'user'             => $entries->first()->user,
                    'branch'           => $entries->first()->branch,
                    'time_in'          => $timeIn?->logged_at,
                    'time_out'         => $timeOut?->logged_at,
                    'late_minutes'     => $timeIn?->late_minutes ?? 0,
                    'undertime_minutes'=> $timeOut?->undertime_minutes ?? 0,
                    'overtime_minutes' => $timeOut?->overtime_minutes ?? 0,
                    'status'           => $timeIn?->status ?? 'pending',
                ];
            })
            ->values();
    }

    public function updatedDate(): void
    {
        unset($this->logs, $this->summary, $this->employeeRows);
    }

    public function updatedBranchId(): void
    {
        unset($this->logs, $this->summary, $this->employeeRows);
    }

    public function render()
    {
        return view('livewire.reports.attendance-summary');
    }
}
