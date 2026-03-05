<?php

use App\Enums\SessionStatus;
use App\Enums\SessionType;
use App\Enums\UserRole;
use App\Models\Session;

it('allows front desk to create client accounts', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $this->post(route('front-desk.clients.store'), [
        'first_name' => 'New',
        'middle_name' => null,
        'last_name' => 'Client',
        'address' => '123 Main St',
        'contact_no' => '09170000000',
        'gender' => 'male',
        'date_of_birth' => '2010-01-01',
        'age' => 14,
        'parent_name' => 'Parent Name',
        'email' => 'new-client@example.com',
    ])->assertRedirect();

    $this->assertDatabaseHas('users', [
        'email' => 'new-client@example.com',
        'role' => UserRole::Client->value,
    ]);
});

it('requires parent name when front desk creates a client', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $this->post(route('front-desk.clients.store'), [
        'first_name' => 'New',
        'middle_name' => null,
        'last_name' => 'Client',
        'address' => '123 Main St',
        'contact_no' => '09170000000',
        'gender' => 'male',
        'date_of_birth' => '2010-01-01',
        'age' => 14,
        'parent_name' => '',
        'email' => 'new-client@example.com',
    ])->assertSessionHasErrors('parent_name');
});

it('creates a valid single schedule within business hours', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    $this->post(route('front-desk.sessions.store'), [
        'date' => '2026-03-02',
        'time' => '10:00',
        'type' => SessionType::Initial->value,
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'description' => 'Initial assessment',
        'schedule_mode' => 'single',
    ])->assertRedirect(route('front-desk.dashboard', absolute: false));

    expect(Session::query()
        ->whereDate('date', '2026-03-02')
        ->where('time', '10:00:00')
        ->where('client_id', $client->id)
        ->where('therapist_id', $therapist->id)
        ->where('status', SessionStatus::Pending->value)
        ->exists())->toBeTrue();
});

it('rejects scheduling outside operating hours', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    $this->post(route('front-desk.sessions.store'), [
        'date' => '2026-03-07',
        'time' => '10:00',
        'type' => SessionType::Regular->value,
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'description' => 'Saturday slot',
        'schedule_mode' => 'single',
    ])->assertSessionHasErrors('time');

    expect(Session::query()
        ->whereDate('date', '2026-03-07')
        ->where('time', '10:00:00')
        ->exists())->toBeFalse();
});

it('prevents therapist and client overlap for single scheduling', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    Session::factory()->create([
        'date' => '2026-03-02',
        'time' => '11:00:00',
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'status' => SessionStatus::Pending,
    ]);

    $this->post(route('front-desk.sessions.store'), [
        'date' => '2026-03-02',
        'time' => '11:00',
        'type' => SessionType::Regular->value,
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'description' => 'Overlap attempt',
        'schedule_mode' => 'single',
    ])->assertSessionHasErrors('time');
});

it('creates repeat schedules and skips conflicted dates', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    Session::factory()->create([
        'date' => '2026-03-03',
        'time' => '09:00:00',
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'status' => SessionStatus::Pending,
    ]);

    $this->post(route('front-desk.sessions.store'), [
        'date' => '2026-03-02',
        'time' => '09:00',
        'type' => SessionType::Regular->value,
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'description' => 'Repeat plan',
        'schedule_mode' => 'repeat',
        'repeat_days' => 3,
    ])->assertRedirect(route('front-desk.dashboard', absolute: false));

    expect(Session::query()->where('client_id', $client->id)->count())->toBe(3);
    expect(Session::query()->where('client_id', $client->id)->whereDate('date', '2026-03-04')->exists())->toBeTrue();
});

it('creates repeat weekly schedules', function (): void {
    signInAs(UserRole::FrontDesk, ['must_change_password' => false]);

    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    $this->post(route('front-desk.sessions.store'), [
        'date' => '2026-03-02',
        'time' => '10:00',
        'type' => SessionType::Regular->value,
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'description' => 'Weekly plan',
        'schedule_mode' => 'repeat_weekly',
        'repeat_days' => 3,
    ])->assertRedirect(route('front-desk.dashboard', absolute: false));

    expect(Session::query()->where('client_id', $client->id)->count())->toBe(3);
    expect(Session::query()->where('client_id', $client->id)->whereDate('date', '2026-03-09')->exists())->toBeTrue();
    expect(Session::query()->where('client_id', $client->id)->whereDate('date', '2026-03-16')->exists())->toBeTrue();
});

it('allows front desk to update pending session schedule fields', function (): void {
    $frontDesk = signInAs(UserRole::FrontDesk, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    $session = Session::factory()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'date' => '2026-03-02',
        'time' => '09:00:00',
        'status' => SessionStatus::Pending,
    ]);

    $this->actingAs($frontDesk)
        ->patch(route('front-desk.sessions.update', $session), [
            'date' => '2026-03-03',
            'time' => '10:00',
            'type' => SessionType::Regular->value,
            'client_id' => $client->id,
            'therapist_id' => $therapist->id,
            'description' => 'Updated by front desk',
        ])->assertRedirect(route('front-desk.sessions.show', $session, absolute: false));

    expect($session->refresh()->description)->toBe('Updated by front desk');
    expect($session->refresh()->date->format('Y-m-d'))->toBe('2026-03-03');
});

it('prevents front desk from updating non-pending sessions', function (): void {
    $frontDesk = signInAs(UserRole::FrontDesk, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    $session = Session::factory()->completed()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
    ]);

    $this->actingAs($frontDesk)
        ->patch(route('front-desk.sessions.update', $session), [
            'date' => '2026-03-03',
            'time' => '10:00',
            'type' => SessionType::Regular->value,
            'client_id' => $client->id,
            'therapist_id' => $therapist->id,
            'description' => 'Should fail',
        ])->assertForbidden();
});
