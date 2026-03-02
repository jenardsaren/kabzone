<?php

use App\Enums\UserRole;

it('redirects users to their role dashboard', function (UserRole $role, string $expectedRoute): void {
    $user = signInAs($role, ['must_change_password' => false]);

    $this->get('/dashboard')
        ->assertRedirect(route($expectedRoute, absolute: false));

    expect($user->role->value)->toBe($role->value);
})->with([
    'admin' => [UserRole::Admin, 'admin.dashboard'],
    'front desk' => [UserRole::FrontDesk, 'front-desk.dashboard'],
    'therapist' => [UserRole::Therapist, 'therapist.dashboard'],
    'assistant' => [UserRole::Assistant, 'assistant.dashboard'],
    'client' => [UserRole::Client, 'client.dashboard'],
]);

it('forbids cross-role route access', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $this->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('forces password change before role dashboard access', function (): void {
    signInAs(UserRole::Therapist, ['must_change_password' => true]);

    $this->get('/dashboard')
        ->assertRedirect(route('password.required.edit', absolute: false));

    $this->get(route('password.required.edit'))
        ->assertSuccessful();
});
