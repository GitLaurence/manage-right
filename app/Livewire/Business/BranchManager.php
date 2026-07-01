<?php

namespace App\Livewire\Business;

use App\Models\Branch;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Branches')]
class BranchManager extends Component
{
    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $branchName = '';

    #[Validate('nullable|string|max:500')]
    public string $address = '';

    #[Validate('required|string|max:100')]
    public string $timezone = 'Asia/Manila';

    #[Computed]
    public function business()
    {
        return Auth::user()->currentBusiness;
    }

    #[Computed]
    public function branches()
    {
        return $this->business->branches()->latest()->get();
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'branchName', 'address', 'timezone');
        $this->timezone = 'Asia/Manila';
        Flux::modal('branch-form')->show();
    }

    public function openEdit(Branch $branch): void
    {
        $this->editingId = $branch->id;
        $this->branchName = $branch->name;
        $this->address = $branch->address ?? '';
        $this->timezone = $branch->timezone;
        Flux::modal('branch-form')->show();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->branchName,
            'address' => $this->address ?: null,
            'timezone' => $this->timezone,
        ];

        if ($this->editingId) {
            $branch = Branch::find($this->editingId);
            $branch->update($data);
            Flux::toast(variant: 'success', text: __('Branch updated.'));
        } else {
            $this->business->branches()->create($data);
            Flux::toast(variant: 'success', text: __('Branch added.'));
        }

        $this->reset('editingId', 'branchName', 'address');
        $this->timezone = 'Asia/Manila';
        Flux::modal('branch-form')->close();
        unset($this->branches);
    }

    public function delete(Branch $branch): void
    {
        $branch->delete();
        Flux::toast(variant: 'success', text: __('Branch deleted.'));
        unset($this->branches);
    }

    public function render()
    {
        return view('livewire.business.branch-manager');
    }
}
