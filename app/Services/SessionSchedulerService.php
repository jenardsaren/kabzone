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
            foreach ($dates as $date) {
                if (! $this->isWithinOperatingHours($date, $data['time'])) {
                    $skipped[] = [
                        'date' => $date->toDateString(),
                        'reason' => 'outside_operating_hours',
                    ];

                    continue;
                }

                if ($this->hasConflict($date, $data['time'], $data['therapist_id'], $data['client_id'])) {
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
            throw ValidationException::withMessages([
                'time' => 'The selected schedule conflicts with an existing session.',
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

    public function hasConflict(CarbonInterface $date, string $time, int $therapistId, int $clientId): bool
    {
        return Session::query()
            ->whereDate('date', $date->toDateString())
            ->where('time', $this->normalizeTimeValue($time))
            ->where('status', '!=', SessionStatus::Cancelled->value)
            ->where(function ($query) use ($therapistId, $clientId): void {
                $query->where('therapist_id', $therapistId)
                    ->orWhere('client_id', $clientId);
            })
            ->exists();
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
