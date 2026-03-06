<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Models\Session;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class AttendanceMetricsService
{
    public const string TIMEZONE = 'Asia/Manila';

    /**
     * @return array{today: int, week: int, month: int}
     */
    public function getMetrics(): array
    {
        $now = CarbonImmutable::now(self::TIMEZONE);

        return [
            'today' => $this->attendedQuery()
                ->whereDate('date', $now->toDateString())
                ->count(),
            'week' => $this->attendedQuery()
                ->whereBetween('date', [
                    $now->startOfWeek(CarbonInterface::MONDAY)->toDateString(),
                    $now->endOfWeek(CarbonInterface::SUNDAY)->toDateString(),
                ])
                ->count(),
            'month' => $this->attendedQuery()
                ->whereBetween('date', [
                    $now->startOfMonth()->toDateString(),
                    $now->endOfMonth()->toDateString(),
                ])
                ->count(),
        ];
    }

    private function attendedQuery(): Builder
    {
        return Session::query()
            ->where('status', SessionStatus::Completed->value);
    }
}
