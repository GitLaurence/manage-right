<?php

namespace App\Livewire\Business;

use App\Models\Invitation;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Invite Employee')]
class InviteEmployee extends Component
{
    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|in:manager,employee')]
    public string $role = 'employee';

    #[Validate('nullable|exists:branches,id')]
    public ?int $branchId = null;

    #[Validate('nullable|string|max:255')]
    public string $position = '';

    public ?string $inviteLink = null;

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
    public function pendingInvitations()
    {
        return Invitation::where('business_id', $this->business->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with('branch')
            ->latest()
            ->get();
    }

    public function send(): void
    {
        $this->validate();

        $existing = Invitation::where('business_id', $this->business->id)
            ->where('email', $this->email)
            ->whereNull('accepted_at')
            ->first();

        if ($existing) {
            $existing->delete();
        }

        $invitation = Invitation::generate([
            'business_id' => $this->business->id,
            'branch_id' => $this->branchId,
            'invited_by' => Auth::id(),
            'email' => $this->email,
            'role' => $this->role,
            'position' => $this->position ?: null,
        ]);

        $this->inviteLink = route('invitations.accept', $invitation->token);

        $this->reset('email', 'position', 'branchId');
        $this->role = 'employee';

        Flux::toast(variant: 'success', text: __('Invitation created.'));

        unset($this->pendingInvitations);
    }

    public function cancel(Invitation $invitation): void
    {
        $invitation->delete();
        $this->inviteLink = null;
        Flux::toast(text: __('Invitation cancelled.'));
        unset($this->pendingInvitations);
    }

    public function render()
    {
        return view('livewire.business.invite-employee');
    }
}
