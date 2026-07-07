<?php

use App\Models\BusinessUser;
use App\Models\Invitation;
use App\Models\User;

test('guest visiting an invitation link is redirected to register with token in session', function () {
    ['business' => $business, 'branch' => $branch] = createBusinessWithOwner();

    $invitation = Invitation::factory()->create([
        'business_id' => $business->id,
        'branch_id' => $branch->id,
    ]);

    $response = $this->get(route('invitations.accept', $invitation->token));

    $response->assertRedirect(route('register'));
    expect(session('invitation_token'))->toBe($invitation->token);
});

test('authenticated user accepting an invitation joins the business', function () {
    ['business' => $business, 'branch' => $branch] = createBusinessWithOwner();

    $invitation = Invitation::factory()->create([
        'business_id' => $business->id,
        'branch_id' => $branch->id,
        'role' => 'manager',
    ]);

    $newUser = User::factory()->create();
    $this->actingAs($newUser);

    $response = $this->get(route('invitations.accept', $invitation->token));

    $response->assertRedirect(route('dashboard'));

    expect(BusinessUser::where('user_id', $newUser->id)->where('business_id', $business->id)->where('role', 'manager')->exists())->toBeTrue();
    expect($newUser->fresh()->current_business_id)->toBe($business->id);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('a user who already belongs to another business can still accept an invitation to a new one', function () {
    ['user' => $existingOwner] = createBusinessWithOwner();
    ['business' => $newBusiness, 'branch' => $newBranch] = createBusinessWithOwner();

    $invitation = Invitation::factory()->create([
        'business_id' => $newBusiness->id,
        'branch_id' => $newBranch->id,
        'role' => 'employee',
    ]);

    $this->actingAs($existingOwner);

    $this->get(route('invitations.accept', $invitation->token))
        ->assertRedirect(route('dashboard'));

    expect(BusinessUser::where('user_id', $existingOwner->id)->where('business_id', $newBusiness->id)->exists())->toBeTrue();
});
