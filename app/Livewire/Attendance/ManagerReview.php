<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceLog;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Attendance Review')]
class ManagerReview extends Component
{
    public string $date;
    public ?int $branchId = null;
    public ?int $flaggingId = null;

    #[Validate('nullable|string|max:500')]
    public string $managerNote = '';

    public function mount(): void
    {
        $this->date = today()->toDateString();
        $this->branchId = Auth::user()->currentBusiness?->branches()->value('id');
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
        return AttendanceLog::with(['user', 'branch'])
            ->where('business_id', $this->business->id)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereDate('logged_at', $this->date)
            ->orderBy('logged_at')
            ->get()
            ->map(function ($log) {
                $log->selfie_url = $log->selfieUrl();
                return $log;
            });
    }

    public function approve(int $logId): void
    {
        AttendanceLog::find($logId)->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'manager_note' => null,
        ]);

        unset($this->logs);
        Flux::toast(variant: 'success', text: __('Entry approved.'));
    }

    public function openFlag(int $logId): void
    {
        $this->flaggingId = $logId;
        $this->managerNote = '';
        Flux::modal('flag-modal')->show();
    }

    public function flag(): void
    {
        $this->validate(['managerNote' => 'nullable|string|max:500']);

        AttendanceLog::find($this->flaggingId)->update([
            'status' => 'flagged',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'manager_note' => $this->managerNote ?: null,
        ]);

        $this->flaggingId = null;
        $this->managerNote = '';
        unset($this->logs);

        Flux::modal('flag-modal')->close();
        Flux::toast(text: __('Entry flagged.'));
    }

    public function updatedDate(): void
    {
        unset($this->logs);
    }

    public function updatedBranchId(): void
    {
        unset($this->logs);
    }

    public function render()
    {
        return view('livewire.attendance.manager-review');
    }
}
