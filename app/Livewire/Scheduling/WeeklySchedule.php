<?php

namespace App\Livewire\Scheduling;

use App\Models\Branch;
use App\Models\BusinessUser;
use App\Models\Schedule;
use App\Models\ScheduleEntry;
use App\Models\ShiftTemplate;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Weekly Schedule')]
class WeeklySchedule extends Component
{
    public string $weekStart;
    public ?int $branchId = null;

    public function mount(): void
    {
        $this->weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
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
    public function weekDates(): array
    {
        $start = Carbon::parse($this->weekStart);
        return array_map(fn ($i) => $start->copy()->addDays($i), range(0, 6));
    }

    #[Computed]
    public function employees()
    {
        return BusinessUser::where('business_id', $this->business->id)
            ->whereIn('role', ['employee', 'manager'])
            ->with('user')
            ->get();
    }

    #[Computed]
    public function shiftTemplates()
    {
        return $this->business->shiftTemplates()->orderBy('start_time')->get();
    }

    #[Computed]
    public function schedule(): ?Schedule
    {
        if (! $this->branchId) {
            return null;
        }

        return Schedule::firstOrCreate(
            ['branch_id' => $this->branchId, 'week_start' => $this->weekStart],
        );
    }

    #[Computed]
    public function entries(): array
    {
        if (! $this->schedule?->id) {
            return [];
        }

        return $this->schedule->entries()
            ->with('shiftTemplate')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($group) => $group->keyBy(fn ($e) => Carbon::parse($e->date)->toDateString()))
            ->all();
    }

    public function previousWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subWeek()->toDateString();
        $this->clearCache();
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addWeek()->toDateString();
        $this->clearCache();
    }

    public function assign(int $templateId, int $userId, string $date): void
    {
        $template = ShiftTemplate::find($templateId);
        if (! $template || ! $this->schedule) {
            return;
        }

        ScheduleEntry::updateOrCreate(
            ['schedule_id' => $this->schedule->id, 'user_id' => $userId, 'date' => $date],
            [
                'shift_template_id' => $template->id,
                'start_time' => $template->start_time,
                'end_time' => $template->end_time,
                'break_minutes' => $template->break_minutes,
            ]
        );

        $this->clearCache();
    }

    public function removeEntry(int $userId, string $date): void
    {
        if (! $this->schedule) {
            return;
        }

        ScheduleEntry::where('schedule_id', $this->schedule->id)
            ->where('user_id', $userId)
            ->where('date', $date)
            ->delete();

        $this->clearCache();
    }

    public function publish(): void
    {
        if (! $this->schedule) {
            return;
        }

        $this->schedule->update(['published_at' => now()]);
        $this->clearCache();

        Flux::toast(variant: 'success', text: __('Schedule published.'));
    }

    public function unpublish(): void
    {
        if (! $this->schedule) {
            return;
        }

        $this->schedule->update(['published_at' => null]);
        $this->clearCache();

        Flux::toast(text: __('Schedule unpublished.'));
    }

    private function clearCache(): void
    {
        unset($this->schedule, $this->entries);
    }

    public function render()
    {
        return view('livewire.scheduling.weekly-schedule');
    }
}
