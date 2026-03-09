<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Models\Session;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessionSchedulerService
{
    public const string TIMEZONE = 'Asia/Manila';

    /**
     * @param array{
     *     date: string,
     *     time: string,
     *     type: string,
     *     client_id: int,
     *     therapist_id: int,
     *     assistant_id?: ?int,
     *     description?: ?string,
     *     payment_status?: string,
     *     schedule_mode: string,
     *     repeat_days?: ?int
     * } $data
     * @return array{created: Collection<int, Session>, skipped: list<array{date: string, reason: string}>}
     */
    public function schedule(array $data): array
    {
        $dates = $this->resolveDates($data);
        $created = new Collection;
        $skipped = [];

        DB::transaction(function () use ($data, $dates, $created, &$skipped): void {
            $assistantId = $data['assistant_id'] ?? null;

            foreach ($dates as $date) {
                if (! $this->isWithinOperatingHours($date, $data['time'])) {
                    $skipped[] = [
                        'date' => $date->toDateString(),
                        'reason' => 'outside_operating_hours',
                    ];

                    continue;
                }

                if ($this->hasConflict(
                    $date,
                    $data['time'],
                    $assistantId,
                    $data['therapist_id'],
                    $data['client_id']
                )) {
                    $skipped[] = [
                        'date' => $date->toDateString(),
                        'reason' => 'conflict',
                    ];

                    continue;
                }

                $session = Session::query()->create([
                    'date' => $date->toDateString(),
                    'time' => $this->normalizeTimeValue($data['time']),
                    'type' => $data['type'],
                    'client_id' => $data['client_id'],
                    'therapist_id' => $data['therapist_id'],
                    'assistant_id' => $data['assistant_id'] ?? null,
                    'description' => $data['description'] ?? null,
                    'payment_status' => $data['payment_status'] ?? 'Unpaid',
                    'status' => SessionStatus::Pending,
                ]);

                $session->note()->create([
                    'content' => null,
                    'bo_cooperative' => null,
                    'bo_calm_regulated' => null,
                    'bo_restless_fidgety' => null,
                    'bo_easily_frustrated' => null,
                    'bo_tantrums' => null,
                    'bo_meltdowns' => null,
                    'bo_avoidant' => null,
                    'bo_aggressive' => null,
                    'bo_other' => null,
                    'bo_other_details' => null,
                ]);
                $created->push($session);
            }
        });

        if ($data['schedule_mode'] === 'single' && $created->isEmpty()) {
            $reason = $this->conflictReason(
                $dates[0],
                $data['time'],
                $data['assistant_id'] ?? null,
                $data['therapist_id'],
                $data['client_id']
            );

            throw ValidationException::withMessages([
                'time' => $reason === 'client'
                    ? 'The selected client already has a session with this therapist at that time.'
                    : 'The selected assistant is already booked for that time.',
            ]);
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    public function isWithinOperatingHours(CarbonInterface $date, string $time): bool
    {
        $hour = (int) substr($time, 0, 2);
        $minute = (int) substr($time, 3, 2);

        if ($minute !== 0) {
            return false;
        }

        if ($date->dayOfWeekIso === CarbonInterface::SATURDAY) {
            return false;
        }

        if ($date->dayOfWeekIso === CarbonInterface::SUNDAY) {
            return $hour >= 13 && $hour <= 20;
        }

        return $hour >= 8 && $hour <= 20;
    }

    public function hasConflict(
        CarbonInterface $date,
        string $time,
        ?int $assistantId,
        ?int $therapistId = null,
        ?int $clientId = null,
        ?int $ignoreSessionId = null
    ): bool {
        return $this->hasAssistantConflict($date, $time, $assistantId, $ignoreSessionId)
            || $this->hasClientTherapistConflict(
                $date,
                $time,
                $therapistId,
                $clientId,
                $ignoreSessionId
            );
    }

    public function hasAssistantConflict(
        CarbonInterface $date,
        string $time,
        ?int $assistantId,
        ?int $ignoreSessionId = null
    ): bool {
        if ($assistantId === null) {
            return false;
        }

        $query = Session::query()
            ->whereDate('date', $date->toDateString())
            ->where('time', $this->normalizeTimeValue($time))
            ->where('status', '!=', SessionStatus::Cancelled->value)
            ->where('assistant_id', $assistantId);

        if ($ignoreSessionId !== null) {
            $query->whereKeyNot($ignoreSessionId);
        }

        return $query->exists();
    }

    public function hasClientTherapistConflict(
        CarbonInterface $date,
        string $time,
        ?int $therapistId,
        ?int $clientId,
        ?int $ignoreSessionId = null
    ): bool {
        if ($therapistId === null || $clientId === null) {
            return false;
        }

        $query = Session::query()
            ->whereDate('date', $date->toDateString())
            ->where('time', $this->normalizeTimeValue($time))
            ->where('status', '!=', SessionStatus::Cancelled->value)
            ->where('therapist_id', $therapistId)
            ->where('client_id', $clientId);

        if ($ignoreSessionId !== null) {
            $query->whereKeyNot($ignoreSessionId);
        }

        return $query->exists();
    }

    private function conflictReason(
        CarbonInterface $date,
        string $time,
        ?int $assistantId,
        ?int $therapistId,
        ?int $clientId
    ): string {
        if ($this->hasAssistantConflict($date, $time, $assistantId)) {
            return 'assistant';
        }

        if ($this->hasClientTherapistConflict($date, $time, $therapistId, $clientId)) {
            return 'client';
        }

        return 'assistant';
    }

    private function normalizeTimeValue(string $time): string
    {
        if (strlen($time) === 5) {
            return $time.':00';
        }

        return $time;
    }

    /**
     * @param  array{date: string, schedule_mode: string, repeat_days?: ?int}  $data
     * @return list<CarbonImmutable>
     */
    private function resolveDates(array $data): array
    {
        $startDate = CarbonImmutable::parse($data['date'], self::TIMEZONE);

        if ($data['schedule_mode'] === 'single') {
            return [$startDate];
        }

        $repeatDays = (int) ($data['repeat_days'] ?? 1);
        $dates = [];

        for ($index = 0; $index < $repeatDays; $index++) {
            if ($data['schedule_mode'] === 'repeat_weekly') {
                $dates[] = $startDate->addWeeks($index);
            } else {
                $dates[] = $startDate->addDays($index);
            }
        }

        return $dates;
    }
}
