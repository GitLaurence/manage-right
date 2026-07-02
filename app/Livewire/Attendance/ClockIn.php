<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceLog;
use App\Models\ScheduleEntry;
use App\Services\SupabaseStorage;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Clock In / Out')]
class ClockIn extends Component
{
    use WithFileUploads;

    public $selfie;
    public float $latitude = 0;
    public float $longitude = 0;
    public bool $done = false;

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function business()
    {
        return $this->user->currentBusiness;
    }

    #[Computed]
    public function todayLogs()
    {
        return AttendanceLog::where('user_id', $this->user->id)
            ->where('business_id', $this->business->id)
            ->whereDate('logged_at', today())
            ->orderBy('logged_at')
            ->get();
    }

    #[Computed]
    public function nextAction(): string
    {
        $logs = $this->todayLogs;

        if ($logs->isEmpty()) {
            return 'time_in';
        }

        $last = $logs->last();

        if ($last->type === 'time_in') {
            return 'time_out';
        }

        return 'done';
    }

    #[Computed]
    public function todaySchedule(): ?ScheduleEntry
    {
        $membership = $this->user->businessMemberships()
            ->where('business_id', $this->business->id)
            ->first();

        if (! $membership?->branch_id) {
            return null;
        }

        $schedule = \App\Models\Schedule::where('branch_id', $membership->branch_id)
            ->where('week_start', Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString())
            ->first();

        if (! $schedule) {
            return null;
        }

        return ScheduleEntry::where('schedule_id', $schedule->id)
            ->where('user_id', $this->user->id)
            ->where('date', today()->toDateString())
            ->first();
    }

    public function record(): void
    {
        $lock = Cache::lock("clock-in:{$this->user->id}:{$this->business->id}", 10);

        if (! $lock->get()) {
            Flux::toast(text: __('Please wait a moment and try again.'));
            return;
        }

        try {
            // Re-check inside the lock so a concurrent double-tap can't both pass this check.
            unset($this->todayLogs, $this->nextAction);
            $action = $this->nextAction;

            if ($action === 'done') {
                Flux::toast(text: __('You have already completed your shift today.'));
                return;
            }

            $membership = $this->user->businessMemberships()
                ->where('business_id', $this->business->id)
                ->first();

            $timezone = $membership?->branch?->timezone ?? config('app.timezone');
            $now = Carbon::now($timezone);
            $selfiePath = null;

            // Upload selfie to Supabase Storage
            if ($this->selfie && config('services.supabase.service_role_key')) {
                try {
                    $filename = $this->user->id.'/'.$now->format('Y-m-d').'/'.$now->format('His').'.jpg';
                    app(SupabaseStorage::class)->upload($filename, file_get_contents($this->selfie->getRealPath()));
                    $selfiePath = $filename;
                } catch (\Throwable $e) {
                    // Non-fatal — log without selfie
                }
            }

            $schedule = $this->todaySchedule;
            $lateMinutes = null;
            $undertimeMinutes = null;
            $overtimeMinutes = null;

            if ($schedule) {
                if ($action === 'time_in') {
                    $scheduledStart = Carbon::parse($now->toDateString().' '.$schedule->start_time, $timezone);
                    $lateMinutes = $now->gt($scheduledStart) ? (int) $now->diffInMinutes($scheduledStart) : 0;
                }

                if ($action === 'time_out') {
                    $scheduledEnd = Carbon::parse($now->toDateString().' '.$schedule->end_time, $timezone);
                    $undertimeMinutes = $now->lt($scheduledEnd) ? (int) $now->diffInMinutes($scheduledEnd) : 0;
                    $overtimeMinutes = $now->gt($scheduledEnd) ? (int) $now->diffInMinutes($scheduledEnd) : 0;
                }
            }

            AttendanceLog::create([
                'user_id' => $this->user->id,
                'business_id' => $this->business->id,
                'branch_id' => $membership?->branch_id,
                'schedule_entry_id' => $schedule?->id,
                'type' => $action,
                'selfie_path' => $selfiePath,
                'latitude' => $this->latitude ?: null,
                'longitude' => $this->longitude ?: null,
                'logged_at' => $now,
                'late_minutes' => $lateMinutes,
                'undertime_minutes' => $undertimeMinutes,
                'overtime_minutes' => $overtimeMinutes,
            ]);
        } finally {
            $lock->release();
        }

        $this->selfie = null;
        $this->done = true;
        unset($this->todayLogs, $this->nextAction);

        Flux::toast(
            variant: 'success',
            text: $action === 'time_in' ? __('Time-in recorded!') : __('Time-out recorded!'),
        );
    }

    public function render()
    {
        return view('livewire.attendance.clock-in');
    }
}
