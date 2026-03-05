<?php

namespace App\Http\Controllers\Therapist;

use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Services\SessionSchedulerService;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = CarbonImmutable::now(SessionSchedulerService::TIMEZONE);
        $today = $now->toDateString();
        $tomorrow = $now->addDay()->toDateString();
        $yesterday = $now->subDay()->toDateString();

        $therapistSessions = auth()->user()
            ->therapistSessions()
            ->with(['client', 'assistant']);

        $todaySessions = (clone $therapistSessions)
            ->whereDate('date', $today)
            ->recent()
            ->get();

        $upcomingSessions = (clone $therapistSessions)
            ->whereDate('date', '>=', $tomorrow)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $pastSessions = (clone $therapistSessions)
            ->whereDate('date', '<=', $yesterday)
            ->recent()
            ->get();

        return view('therapist.dashboard', [
            'todaySessions' => $todaySessions,
            'upcomingSessions' => $upcomingSessions,
            'pastSessions' => $pastSessions,
        ]);
    }
}
