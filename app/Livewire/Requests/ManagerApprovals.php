<?php

namespace App\Livewire\Requests;

use App\Models\EmployeeRequest;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Request Approvals')]
class ManagerApprovals extends Component
{
    public string $statusFilter = 'pending';
    public ?int $branchId = null;
    public ?int $reviewingId = null;
    public string $remarks = '';

    public function mount(): void
    {
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
    public function requests()
    {
        return EmployeeRequest::with(['user', 'branch', 'reviewer'])
            ->where('business_id', $this->business->id)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->get();
    }

    public function approve(int $id): void
    {
        EmployeeRequest::find($id)->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'manager_remarks' => null,
        ]);

        unset($this->requests);
        Flux::toast(variant: 'success', text: __('Request approved.'));
    }

    public function openReject(int $id): void
    {
        $this->reviewingId = $id;
        $this->remarks     = '';
        Flux::modal('reject-modal')->show();
    }

    public function reject(): void
    {
        $this->validate(['remarks' => 'nullable|string|max:1000']);

        EmployeeRequest::find($this->reviewingId)->update([
            'status'          => 'rejected',
            'reviewed_by'     => Auth::id(),
            'reviewed_at'     => now(),
            'manager_remarks' => $this->remarks ?: null,
        ]);

        $this->reviewingId = null;
        $this->remarks     = '';
        unset($this->requests);

        Flux::modal('reject-modal')->close();
        Flux::toast(text: __('Request rejected.'));
    }

    public function updatedStatusFilter(): void
    {
        unset($this->requests);
    }

    public function updatedBranchId(): void
    {
        unset($this->requests);
    }

    public function render()
    {
        return view('livewire.requests.manager-approvals');
    }
}
