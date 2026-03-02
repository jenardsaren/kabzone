<?php

namespace App\Http\Controllers\Assistant;

use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Services\SessionSchedulerService;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = CarbonImmutable::now(SessionSchedulerService::TIMEZONE)->toDateString();

        $sessions = auth()->user()
            ->assistantSessions()
            ->with(['client', 'therapist', 'tasks'])
            ->whereDate('date', $today)
            ->where('status', SessionStatus::Pending->value)
            ->recent()
            ->get();

        return view('assistant.dashboard', [
            'sessions' => $sessions,
        ]);
    }
}
