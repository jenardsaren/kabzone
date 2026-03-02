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
        $today = CarbonImmutable::now(SessionSchedulerService::TIMEZONE)->toDateString();

        $sessions = auth()->user()
            ->therapistSessions()
            ->with(['client', 'assistant'])
            ->whereDate('date', $today)
            ->where('status', SessionStatus::Pending->value)
            ->recent()
            ->get();

        return view('therapist.dashboard', [
            'sessions' => $sessions,
        ]);
    }
}
