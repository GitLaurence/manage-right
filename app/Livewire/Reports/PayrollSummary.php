<?php

namespace App\Livewire\Reports;

use App\Models\AttendanceLog;
use App\Models\EmployeeRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Payroll Summary')]
class PayrollSummary extends Component
{
    public string $weekStart;
    public ?int $branchId = null;

    public function mount(): void
    {
        $this->weekStart = today()->startOfWeek()->toDateString();
        $this->branchId  = Auth::user()->currentBusiness?->branches()->value('id');
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
    public function weekEnd(): string
    {
        return Carbon::parse($this->weekStart)->endOfWeek()->toDateString();
    }

    #[Computed]
    public function rows(): \Illuminate\Support\Collection
    {
        $start = Carbon::parse($this->weekStart);
        $end   = Carbon::parse($this->weekEnd);

        $logs = AttendanceLog::with(['user', 'branch'])
            ->where('business_id', $this->business->id)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereBetween('logged_at', [$start->startOfDay(), $end->endOfDay()])
            ->get();

        $requests = EmployeeRequest::with('user')
            ->where('business_id', $this->business->id)
            ->where('status', 'approved')
            ->where('type', 'leave')
            ->where(fn ($q) => $q
                ->whereBetween('from_date', [$start, $end])
                ->orWhereBetween('to_date', [$start, $end])
            )
            ->get();

        $approvedLeaveByUser = $requests->groupBy('user_id');

        return $logs->groupBy('user_id')->map(function ($entries) use ($approvedLeaveByUser) {
            $user = $entries->first()->user;

            $timesIn  = $entries->where('type', 'time_in');
            $timesOut = $entries->where('type', 'time_out');

            $totalLateMinutes      = $timesIn->sum('late_minutes');
            $totalUndertimeMinutes = $timesOut->sum('undertime_minutes');
            $totalOvertimeMinutes  = $timesOut->sum('overtime_minutes');

            $daysPresent = $entries->groupBy(fn ($l) => $l->logged_at->toDateString())->count();
            $leaveDays   = $approvedLeaveByUser->get($user->id, collect())->sum(function ($req) {
                return $req->from_date->diffInDays($req->to_date ?? $req->from_date) + 1;
            });

            return (object) [
                'user'               => $user,
                'branch'             => $entries->first()->branch,
                'days_present'       => $daysPresent,
                'leave_days'         => $leaveDays,
                'late_minutes'       => $totalLateMinutes,
                'undertime_minutes'  => $totalUndertimeMinutes,
                'overtime_minutes'   => $totalOvertimeMinutes,
                'late_hours'         => round($totalLateMinutes / 60, 2),
                'undertime_hours'    => round($totalUndertimeMinutes / 60, 2),
                'overtime_hours'     => round($totalOvertimeMinutes / 60, 2),
            ];
        })->values();
    }

    public function previousWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->toDateString();
        unset($this->rows);
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->toDateString();
        unset($this->rows);
    }

    public function updatedBranchId(): void
    {
        unset($this->rows);
    }

    public function exportCsv(): StreamedResponse
    {
        $rows      = $this->rows;
        $weekStart = $this->weekStart;
        $weekEnd   = $this->weekEnd;
        $business  = $this->business->name;

        return response()->streamDownload(function () use ($rows, $weekStart, $weekEnd, $business) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ["Payroll Summary — {$business} — {$weekStart} to {$weekEnd}"]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Employee', 'Branch', 'Days Present', 'Leave Days',
                'Late (hrs)', 'Undertime (hrs)', 'Overtime (hrs)',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->user->name,
                    $row->branch?->name ?? '',
                    $row->days_present,
                    $row->leave_days,
                    $row->late_hours,
                    $row->undertime_hours,
                    $row->overtime_hours,
                ]);
            }

            fclose($handle);
        }, "payroll-{$weekStart}-to-{$weekEnd}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.reports.payroll-summary');
    }
}
