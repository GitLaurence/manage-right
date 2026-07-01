<?php

namespace App\Livewire\Dashboard;

use App\Models\ActivityLog;
use App\Models\AttendanceLog;
use App\Models\EmployeeRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Overview extends Component
{
    #[Computed]
    public function business()
    {
        return Auth::user()->currentBusiness;
    }

    #[Computed]
    public function branches()
    {
        return $this->business->branches()->withCount([
            'memberships as employee_count',
        ])->orderBy('name')->get();
    }

    #[Computed]
    public function todayStats(): array
    {
        $today = today();

        $logs = AttendanceLog::where('business_id', $this->business->id)
            ->whereDate('logged_at', $today)
            ->get();

        $timesIn = $logs->where('type', 'time_in');

        return [
            'present'   => $timesIn->count(),
            'late'      => $timesIn->filter(fn ($l) => $l->isLate())->count(),
            'pending'   => $logs->where('status', 'pending')->count(),
            'flagged'   => $logs->where('status', 'flagged')->count(),
        ];
    }

    #[Computed]
    public function pendingRequests(): int
    {
        return EmployeeRequest::where('business_id', $this->business->id)
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function branchSnapshots(): \Illuminate\Support\Collection
    {
        $today = today();

        $logs = AttendanceLog::where('business_id', $this->business->id)
            ->whereDate('logged_at', $today)
            ->where('type', 'time_in')
            ->get()
            ->groupBy('branch_id');

        return $this->branches->map(function ($branch) use ($logs) {
            $branchLogs = $logs->get($branch->id, collect());
            return (object) [
                'branch'        => $branch,
                'employee_count'=> $branch->employee_count,
                'present'       => $branchLogs->count(),
                'late'          => $branchLogs->filter(fn ($l) => $l->isLate())->count(),
            ];
        });
    }

    #[Computed]
    public function recentActivity(): \Illuminate\Support\Collection
    {
        return ActivityLog::with(['user', 'branch'])
            ->where('business_id', $this->business->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.overview');
    }
}
