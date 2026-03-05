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
});
