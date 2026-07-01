<?php

namespace App\Livewire\Onboarding;

use App\Models\Business;
use App\Models\BusinessUser;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Set Up Your Business')]
class BusinessSetup extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:100')]
    public string $type = '';

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        $business = Business::create([
            'owner_id' => $user->id,
            'name' => $this->name,
            'type' => $this->type,
        ]);

        BusinessUser::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'role' => 'owner',
        ]);

        $user->update(['current_business_id' => $business->id]);

        Flux::toast(variant: 'success', text: __('Business created! Now add your first branch.'));

        $this->redirect(route('branches.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding.business-setup');
    }
}
