<?php

use App\Enums\SessionStatus;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Session;
use App\Models\Task;

it('prevents therapist completion without assistant and tasks', function (): void {
    $therapist = signInAs(UserRole::Therapist, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'status' => SessionStatus::Pending,
    ]);

    $this->patch(route('therapist.sessions.status.update', $session), [
        'status' => SessionStatus::Completed->value,
    ])->assertSessionHasErrors('status');

    expect($session->refresh()->status->value)->toBe(SessionStatus::Pending->value);
});

it('allows therapist to complete once assistant and tasks exist', function (): void {
    $therapist = signInAs(UserRole::Therapist, ['must_change_password' => false]);
    $assistant = makeUser(UserRole::Assistant);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'assistant_id' => $assistant->id,
        'status' => SessionStatus::Pending,
    ]);

    Task::factory()->create([
        'session_id' => $session->id,
        'status' => TaskStatus::Pending,
    ]);

    $this->patch(route('therapist.sessions.status.update', $session), [
        'status' => SessionStatus::Completed->value,
    ])->assertRedirect(route('therapist.sessions.show', $session, absolute: false));

    expect($session->refresh()->status->value)->toBe(SessionStatus::Completed->value);
});

it('allows assistants to update only assigned session tasks', function (): void {
    $assistant = signInAs(UserRole::Assistant, ['must_change_password' => false]);
    $otherAssistant = makeUser(UserRole::Assistant);
    $therapist = makeUser(UserRole::Therapist);
    $client = makeUser(UserRole::Client);

    $assignedSession = Session::factory()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'assistant_id' => $assistant->id,
        'status' => SessionStatus::Pending,
    ]);

    $assignedTask = Task::factory()->create([
        'session_id' => $assignedSession->id,
        'status' => TaskStatus::Pending,
    ]);

    $this->patch(route('assistant.tasks.status.update', $assignedTask), [
        'status' => TaskStatus::Completed->value,
    ])->assertRedirect();

    expect($assignedTask->refresh()->status->value)->toBe(TaskStatus::Completed->value);

    $unassignedSession = Session::factory()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'assistant_id' => $otherAssistant->id,
        'status' => SessionStatus::Pending,
    ]);

    $unassignedTask = Task::factory()->create([
        'session_id' => $unassignedSession->id,
        'status' => TaskStatus::Pending,
    ]);

    $this->patch(route('assistant.tasks.status.update', $unassignedTask), [
        'status' => TaskStatus::Completed->value,
    ])->assertForbidden();
});

it('limits client visibility to completed own sessions', function (): void {
    $client = signInAs(UserRole::Client, ['must_change_password' => false]);
    $therapist = makeUser(UserRole::Therapist);

    $completedSession = Session::factory()->completed()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
    ]);

    $pendingSession = Session::factory()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'status' => SessionStatus::Pending,
    ]);

    $otherClientSession = Session::factory()->completed()->create([
        'client_id' => makeUser(UserRole::Client)->id,
        'therapist_id' => $therapist->id,
    ]);

    $this->get(route('client.sessions.show', $completedSession))
        ->assertSuccessful();

    $this->get(route('client.sessions.show', $pendingSession))
        ->assertForbidden();

    $this->get(route('client.sessions.show', $otherClientSession))
        ->assertForbidden();
});

it('locks completed sessions for therapist edits while allowing admin override', function (): void {
    $therapist = makeUser(UserRole::Therapist, ['must_change_password' => false]);
    $assistant = makeUser(UserRole::Assistant);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->completed()->create([
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'assistant_id' => $assistant->id,
    ]);

    $this->actingAs($therapist)
        ->patch(route('therapist.sessions.details.update', $session), [
            'assistant_id' => $assistant->id,
            'description' => 'Updated',
            'notes' => 'Changed notes',
        ])->assertForbidden();

    expect($session->refresh()->description)->not->toBe('Updated');

    $admin = makeUser(UserRole::Admin, ['must_change_password' => false]);

    $this->actingAs($admin)
        ->patch(route('admin.sessions.update', $session), [
            'assistant_id' => $assistant->id,
            'description' => 'Admin override applied',
            'notes' => 'Admin corrected details',
            'status' => SessionStatus::Completed->value,
        ])->assertRedirect();

    expect($session->refresh()->description)->toBe('Admin override applied');
});
