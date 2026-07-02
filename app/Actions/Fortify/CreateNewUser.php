<?php

namespace App\Actions\Fortify;

use App\Actions\AcceptInvitation;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        if ($token = Session::pull('invitation_token')) {
            $invitation = Invitation::where('token', $token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if ($invitation) {
                app(AcceptInvitation::class)($user, $invitation);
            }
        }

        return $user;
    }
}
