<?php

use App\Enums\SessionStatus;
use App\Enums\UserRole;
use App\Models\Session;
use App\Models\Task;
use App\Services\AttendanceMetricsService;
use Carbon\CarbonImmutable;

it('shows metrics derived from completed sessions with tasks only', function (): void {
    CarbonImmutable::setTestNow('2026-03-02 09:00:00');

    $admin = signInAs(UserRole::Admin, ['must_change_password' => false]);
    $therapist = makeUser(UserRole::Therapist);
    $client = makeUser(UserRole::Client);

    $todayCompleted = Session::factory()->completed()->create([
        'date' => '2026-03-02',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);
    Task::factory()->create(['session_id' => $todayCompleted->id]);

    $weekCompleted = Session::factory()->completed()->create([
        'date' => '2026-03-04',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);
    Task::factory()->create(['session_id' => $weekCompleted->id]);

    $monthCompleted = Session::factory()->completed()->create([
        'date' => '2026-03-10',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);
    Task::factory()->create(['session_id' => $monthCompleted->id]);

    Session::factory()->completed()->create([
        'date' => '2026-03-02',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    Session::factory()->create([
        'date' => '2026-03-02',
        'status' => SessionStatus::Pending,
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    $metrics = app(AttendanceMetricsService::class)->getMetrics();

    expect($metrics['today'])->toBe(1);
    expect($metrics['week'])->toBe(2);
    expect($metrics['month'])->toBe(3);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Attended Today')
        ->assertSee('Attended This Week')
        ->assertSee('Attended This Month');

    CarbonImmutable::setTestNow();
});
