<?php

use App\Enums\SessionStatus;
use App\Enums\SessionType;
use App\Enums\UserRole;
use App\Models\Session;
use App\Services\SessionSchedulerService;
use Carbon\CarbonImmutable;

it('validates operating-hour boundaries', function (): void {
    $service = app(SessionSchedulerService::class);

    expect($service->isWithinOperatingHours(CarbonImmutable::parse('2026-03-02', 'Asia/Manila'), '08:00'))->toBeTrue();
    expect($service->isWithinOperatingHours(CarbonImmutable::parse('2026-03-02', 'Asia/Manila'), '18:00'))->toBeTrue();
    expect($service->isWithinOperatingHours(CarbonImmutable::parse('2026-03-02', 'Asia/Manila'), '21:00'))->toBeFalse();
    expect($service->isWithinOperatingHours(CarbonImmutable::parse('2026-03-07', 'Asia/Manila'), '10:00'))->toBeFalse();
    expect($service->isWithinOperatingHours(CarbonImmutable::parse('2026-03-08', 'Asia/Manila'), '13:00'))->toBeTrue();
});

it('creates repeat schedules while skipping conflicts', function (): void {
    $service = app(SessionSchedulerService::class);

    $client = makeUser(UserRole::Client);
    $therapist = makeUser(UserRole::Therapist);

    Session::factory()->create([
        'date' => '2026-03-03',
        'time' => '09:00:00',
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'status' => SessionStatus::Pending,
    ]);

    $result = $service->schedule([
        'date' => '2026-03-02',
        'time' => '09:00',
        'type' => SessionType::Regular->value,
        'client_id' => $client->id,
        'therapist_id' => $therapist->id,
        'description' => 'Repeat',
        'schedule_mode' => 'repeat',
        'repeat_days' => 3,
    ]);

    expect($result['created'])->toHaveCount(2);
    expect($result['skipped'])->toHaveCount(1);
    expect($result['skipped'][0]['date'])->toBe('2026-03-03');
});
