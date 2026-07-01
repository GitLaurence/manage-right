<?php

namespace App\Livewire\Requests;

use App\Models\EmployeeRequest;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('My Requests')]
class MyRequests extends Component
{
    public string $type = 'leave';
    public string $leaveType = 'vacation';
    public string $fromDate = '';
    public string $toDate = '';
    public string $hours = '';
    public string $reason = '';
    public bool $showForm = false;

    public function mount(): void
    {
        $this->fromDate = today()->toDateString();
        $this->toDate   = today()->toDateString();
    }

    #[Computed]
    public function business()
    {
        return Auth::user()->currentBusiness;
    }

    #[Computed]
    public function requests()
    {
        return EmployeeRequest::with(['reviewer'])
            ->where('user_id', Auth::id())
            ->where('business_id', $this->business->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function openForm(): void
    {
        $this->reset(['type', 'leaveType', 'hours', 'reason']);
        $this->type      = 'leave';
        $this->leaveType = 'vacation';
        $this->fromDate  = today()->toDateString();
        $this->toDate    = today()->toDateString();
        $this->showForm  = true;
    }

    public function submit(): void
    {
        $rules = [
            'type'   => 'required|in:leave,overtime,undertime',
            'reason' => 'required|string|max:1000',
        ];

        if ($this->type === 'leave') {
            $rules['leaveType'] = 'required|in:sick,vacation,emergency,unpaid';
            $rules['fromDate']  = 'required|date';
            $rules['toDate']    = 'required|date|after_or_equal:fromDate';
        } else {
            $rules['fromDate'] = 'required|date';
            $rules['hours']    = 'required|numeric|min:0.5|max:24';
        }

        $this->validate($rules);

        $membership = $this->business->memberships()
            ->where('user_id', Auth::id())
            ->first();

        EmployeeRequest::create([
            'user_id'     => Auth::id(),
            'business_id' => $this->business->id,
            'branch_id'   => $membership?->branch_id ?? $this->business->branches()->value('id'),
            'type'        => $this->type,
            'leave_type'  => $this->type === 'leave' ? $this->leaveType : null,
            'from_date'   => $this->fromDate,
            'to_date'     => $this->type === 'leave' ? $this->toDate : null,
            'hours'       => $this->type !== 'leave' ? $this->hours : null,
            'reason'      => $this->reason,
        ]);

        unset($this->requests);
        $this->showForm = false;
        Flux::toast(variant: 'success', text: __('Request submitted successfully.'));
    }

    public function cancel(): void
    {
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.requests.my-requests');
    }
}
