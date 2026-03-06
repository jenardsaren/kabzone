<?php

use App\Enums\UserRole;
use App\Models\Session;

it('only updates ef columns when note_section is ef', function (): void {
    $therapist = signInAs(UserRole::Therapist, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->create([
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    $session->note()->create([
        'bo_cooperative' => true,
        'am_activities_and_management' => 'initial activities',
    ]);

    $this->patch(route('therapist.sessions.notes.update', $session), [
        'note_section' => 'ef',
        'ef_sensory_arousal_under' => '1',
        'ef_fine_motor_assistance_independent' => '1',
        'ef_cognitive_specify' => 'Cognitive note',
        'ef_visual_discrimination' => '1',
        'ef_visual_assistance_level_maximal' => '1',
        'ef_visual_assistance_type_physical' => '1',
        'ef_cognitive_remarks' => '  Powered remark  ',
    ])->assertRedirect();

    $note = $session->refresh()->note;

    expect($note->bo_cooperative)->toBeTrue();
    expect($note->am_activities_and_management)->toBe('initial activities');
    expect($note->ef_sensory_arousal_under)->toBeTrue();
    expect($note->ef_fine_motor_assistance_independent)->toBeTrue();
    expect($note->ef_cognitive_specify)->toBe('Cognitive note');
    expect($note->ef_visual_discrimination)->toBeTrue();
    expect($note->ef_visual_assistance_level_maximal)->toBeTrue();
    expect($note->ef_visual_assistance_type_physical)->toBeTrue();
    expect($note->ef_cognitive_remarks)->toBe('Powered remark');
});

it('only updates ei columns when note_section is ei', function (): void {
    $therapist = signInAs(UserRole::Therapist, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->create([
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    $session->note()->create([
        'bo_cooperative' => true,
        'am_activities_and_management' => 'initial activities',
        'ef_visual_discrimination' => false,
    ]);

    $this->patch(route('therapist.sessions.notes.update', $session), [
        'note_section' => 'ei',
        'ei_work_behavior_frustration_tolerance' => '85',
        'ei_visual_discrimination' => '1',
        'ei_cognitive_msri' => '1',
        'ei_language_specify' => 'Language detail',
        'ei_play_specify' => 'Play detail',
    ])->assertRedirect();

    $note = $session->refresh()->note;

    expect($note->bo_cooperative)->toBeTrue();
    expect($note->am_activities_and_management)->toBe('initial activities');
    expect($note->ef_visual_discrimination)->toBeFalse();
    expect($note->ei_work_behavior_frustration_tolerance)->toBe(85);
    expect($note->ei_visual_discrimination)->toBeTrue();
    expect($note->ei_cognitive_msri)->toBeTrue();
    expect($note->ei_language_specify)->toBe('Language detail');
    expect($note->ei_play_specify)->toBe('Play detail');
});

it('only updates plan column when note_section is plan', function (): void {
    $therapist = signInAs(UserRole::Therapist, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->create([
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    $session->note()->create([
        'bo_cooperative' => true,
        'ei_visual_discrimination' => true,
        'plan' => 'Old plan',
    ]);

    $this->patch(route('therapist.sessions.notes.update', $session), [
        'note_section' => 'plan',
        'plan' => 'Updated plan',
    ])->assertRedirect();

    $note = $session->refresh()->note;

    expect($note->bo_cooperative)->toBeTrue();
    expect($note->ei_visual_discrimination)->toBeTrue();
    expect($note->plan)->toBe('Updated plan');
});

it('only updates approval signature when note_section is approval', function (): void {
    $therapist = signInAs(UserRole::Therapist, ['must_change_password' => false]);
    $client = makeUser(UserRole::Client);

    $session = Session::factory()->create([
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    $session->note()->create([
        'plan' => 'Old plan',
        'approval_signature' => null,
    ]);

    $this->patch(route('therapist.sessions.notes.update', $session), [
        'note_section' => 'approval',
        'approval_signature' => 'data:image/png;base64,abc',
    ])->assertRedirect();

    $note = $session->refresh()->note;

    expect($note->plan)->toBe('Old plan');
    expect($note->approval_signature)->toBe('data:image/png;base64,abc');
});
