<?php

namespace App\Actions;

use App\Models\BusinessUser;
use App\Models\Invitation;
use App\Models\User;

class AcceptInvitation
{
    public function __invoke(User $user, Invitation $invitation): void
    {
        if (! $user->businessMemberships()->withoutTenant()->where('business_id', $invitation->business_id)->exists()) {
            BusinessUser::create([
                'user_id' => $user->id,
                'business_id' => $invitation->business_id,
                'branch_id' => $invitation->branch_id,
                'role' => $invitation->role,
                'position' => $invitation->position,
            ]);
        }

        $invitation->update([
            'accepted_at' => now(),
            'email' => $invitation->email ?? $user->email,
        ]);

        $user->update(['current_business_id' => $invitation->business_id]);
    }
}
