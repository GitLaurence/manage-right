<?php

namespace App\Livewire\Scheduling;

use App\Models\ShiftTemplate;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Shift Templates')]
class ShiftTemplates extends Component
{
    public ?int $editingId = null;

    public function mount(): void
    {
        $business = Auth::user()->currentBusiness;

        abort_unless($business && Auth::user()->isManagerOrOwnerOf($business), 403);
    }

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|date_format:H:i')]
    public string $startTime = '08:00';

    #[Validate('required|date_format:H:i')]
    public string $endTime = '17:00';

    #[Validate('required|integer|min:0|max:480')]
    public int $breakMinutes = 60;

    #[Validate('required|in:blue,green,amber,purple,rose,zinc')]
    public string $color = 'blue';

    #[Computed]
    public function business()
    {
        return Auth::user()->currentBusiness;
    }

    #[Computed]
    public function templates()
    {
        return $this->business->shiftTemplates()->orderBy('start_time')->get();
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'name', 'breakMinutes');
        $this->startTime = '08:00';
        $this->endTime = '17:00';
        $this->color = 'blue';
        $this->breakMinutes = 60;
        Flux::modal('template-form')->show();
    }

    public function openEdit(ShiftTemplate $shiftTemplate): void
    {
        $this->editingId = $shiftTemplate->id;
        $this->name = $shiftTemplate->name;
        $this->startTime = substr($shiftTemplate->start_time, 0, 5);
        $this->endTime = substr($shiftTemplate->end_time, 0, 5);
        $this->breakMinutes = $shiftTemplate->break_minutes;
        $this->color = $shiftTemplate->color;
        Flux::modal('template-form')->show();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'break_minutes' => $this->breakMinutes,
            'color' => $this->color,
        ];

        if ($this->editingId) {
            ShiftTemplate::find($this->editingId)->update($data);
            Flux::toast(variant: 'success', text: __('Shift template updated.'));
        } else {
            $this->business->shiftTemplates()->create($data);
            Flux::toast(variant: 'success', text: __('Shift template created.'));
        }

        Flux::modal('template-form')->close();
        unset($this->templates);
    }

    public function delete(ShiftTemplate $shiftTemplate): void
    {
        $shiftTemplate->delete();
        Flux::toast(variant: 'success', text: __('Shift template deleted.'));
        unset($this->templates);
    }

    public function render()
    {
        return view('livewire.scheduling.shift-templates');
    }
}
