<?php

use App\Enums\SessionStatus;
use App\Enums\UserRole;
use App\Models\Session;
use App\Models\Task;
use App\Services\AttendanceMetricsService;
use Carbon\CarbonImmutable;

it('counts day week and month attendance using completed plus tasks formula', function (): void {
    CarbonImmutable::setTestNow('2026-03-05 10:00:00');

    $therapist = makeUser(UserRole::Therapist);
    $client = makeUser(UserRole::Client);

    $today = Session::factory()->completed()->create([
        'date' => '2026-03-05',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);
    Task::factory()->create(['session_id' => $today->id]);

    $week = Session::factory()->completed()->create([
        'date' => '2026-03-03',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);
    Task::factory()->create(['session_id' => $week->id]);

    $month = Session::factory()->completed()->create([
        'date' => '2026-03-20',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);
    Task::factory()->create(['session_id' => $month->id]);

    Session::factory()->completed()->create([
        'date' => '2026-03-05',
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    Session::factory()->create([
        'date' => '2026-03-05',
        'status' => SessionStatus::Pending,
        'therapist_id' => $therapist->id,
        'client_id' => $client->id,
    ]);

    $metrics = app(AttendanceMetricsService::class)->getMetrics();

    expect($metrics['today'])->toBe(1);
    expect($metrics['week'])->toBe(2);
    expect($metrics['month'])->toBe(3);

    CarbonImmutable::setTestNow();
});
