<?php

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
    $response->assertSee($user->parent_name);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Test',
            'middle_name' => 'Middle',
            'last_name' => 'User',
            'address' => '456 Updated Street',
            'contact_no' => '09171234567',
            'gender' => 'male',
            'parent_name' => 'Updated Parent',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test', $user->first_name);
    $this->assertSame('Middle', $user->middle_name);
    $this->assertSame('User', $user->last_name);
    $this->assertSame('456 Updated Street', $user->address);
    $this->assertSame('09171234567', $user->contact_no);
    $this->assertSame(Gender::Male, $user->gender);
    $this->assertSame('Updated Parent', $user->parent_name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('client users must provide parent name when updating profile information', function () {
    $user = User::factory()->client()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'address' => '123 Main Street',
            'contact_no' => '09170000000',
            'gender' => 'female',
            'parent_name' => '',
            'email' => 'test@example.com',
        ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHasErrors('parent_name');
});

test('staff users can update profile without parent name', function () {
    $user = User::factory()->staff(UserRole::Therapist->value)->create([
        'parent_name' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Therapist',
            'middle_name' => null,
            'last_name' => 'User',
            'address' => '789 Staff Street',
            'contact_no' => '09179876543',
            'gender' => 'female',
            'email' => 'therapist@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Therapist', $user->first_name);
    $this->assertSame('User', $user->last_name);
    $this->assertSame('789 Staff Street', $user->address);
    $this->assertSame('09179876543', $user->contact_no);
    $this->assertSame(Gender::Female, $user->gender);
    $this->assertNull($user->parent_name);
    $this->assertSame('therapist@example.com', $user->email);
});

test('updating a user payload to client requires parent name', function () {
    $user = User::factory()->staff(UserRole::Therapist->value)->create([
        'parent_name' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'first_name' => 'Therapist',
            'last_name' => 'User',
            'address' => '789 Staff Street',
            'contact_no' => '09179876543',
            'gender' => 'female',
            'email' => 'therapist@example.com',
            'role' => UserRole::Client->value,
        ]);

    $response->assertRedirect('/profile');
    $response->assertSessionHasErrors('parent_name');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Test',
            'middle_name' => $user->middle_name,
            'last_name' => 'User',
            'address' => '321 Stable Street',
            'contact_no' => '09170123456',
            'gender' => 'male',
            'parent_name' => $user->parent_name,
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
