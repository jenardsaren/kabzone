<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;

it('allows admin to create staff accounts with temporary-password policy', function (): void {
    signInAs(UserRole::Admin, ['must_change_password' => false]);

    $this->post(route('admin.staff.store'), [
        'first_name' => 'Jane',
        'middle_name' => null,
        'last_name' => 'Therapist',
        'address' => '123 Main St',
        'contact_no' => '09170000000',
        'gender' => 'female',
        'email' => 'jane.therapist@example.com',
        'role' => UserRole::Therapist->value,
        'status' => UserStatus::Active->value,
    ])->assertRedirect();

    $staff = User::query()->where('email', 'jane.therapist@example.com')->first();

    expect($staff)->not->toBeNull();
    expect($staff->role->value)->toBe(UserRole::Therapist->value);
    expect($staff->must_change_password)->toBeTrue();
});

it('blocks non-admin users from creating staff accounts', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $this->post(route('admin.staff.store'), [
        'first_name' => 'Blocked',
        'middle_name' => null,
        'last_name' => 'User',
        'address' => '123 Main St',
        'contact_no' => '09170000001',
        'gender' => 'male',
        'email' => 'blocked@example.com',
        'role' => UserRole::Therapist->value,
        'status' => UserStatus::Active->value,
    ])->assertForbidden();
});

it('allows admin to update staff status', function (): void {
    signInAs(UserRole::Admin, ['must_change_password' => false]);
    $staff = makeUser(UserRole::Assistant, ['must_change_password' => false]);

    $this->patch(route('admin.staff.update', $staff), [
        'first_name' => $staff->first_name,
        'middle_name' => $staff->middle_name,
        'last_name' => $staff->last_name,
        'address' => $staff->address,
        'contact_no' => $staff->contact_no,
        'gender' => $staff->gender->value,
        'email' => $staff->email,
        'role' => UserRole::Assistant->value,
        'status' => UserStatus::Inactive->value,
    ])->assertRedirect();

    expect($staff->refresh()->status->value)->toBe(UserStatus::Inactive->value);
});
