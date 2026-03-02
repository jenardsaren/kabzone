<?php

namespace App\Http\Controllers\FrontDesk;

use App\Enums\SessionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontDesk\StoreSessionRequest;
use App\Http\Requests\FrontDesk\UpdateSessionScheduleRequest;
use App\Models\Session;
use App\Models\User;
use App\Services\SessionSchedulerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View
    {
        $clients = User::query()
            ->where('role', UserRole::Client->value)
            ->where('status', UserStatus::Active->value)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $therapists = User::query()
            ->where('role', UserRole::Therapist->value)
            ->where('status', UserStatus::Active->value)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('front-desk.sessions.create', [
            'clients' => $clients,
            'therapists' => $therapists,
        ]);
    }

    public function store(StoreSessionRequest $request, SessionSchedulerService $schedulerService): RedirectResponse
    {
        $result = $schedulerService->schedule($request->validated());

        return redirect()
            ->route('front-desk.dashboard')
            ->with('status', 'session-scheduled')
            ->with('scheduled_count', $result['created']->count())
            ->with('skipped_dates', $result['skipped']);
    }

    public function show(Session $session): View
    {
        $this->authorize('view', $session);

        $session->load(['client', 'therapist', 'assistant', 'tasks']);

        return view('front-desk.sessions.show', [
            'session' => $session,
        ]);
    }

    public function edit(Session $session): View
    {
        $this->authorize('updateSchedule', $session);

        $clients = User::query()
            ->where('role', UserRole::Client->value)
            ->where('status', UserStatus::Active->value)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $therapists = User::query()
            ->where('role', UserRole::Therapist->value)
            ->where('status', UserStatus::Active->value)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('front-desk.sessions.edit', [
            'session' => $session,
            'clients' => $clients,
            'therapists' => $therapists,
        ]);
    }

    public function update(UpdateSessionScheduleRequest $request, Session $session): RedirectResponse
    {
        $validated = $request->validated();

        $session->update([
            ...$validated,
            'time' => strlen($validated['time']) === 5 ? $validated['time'].':00' : $validated['time'],
        ]);

        return redirect()
            ->route('front-desk.sessions.show', $session)
            ->with('status', 'session-updated');
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $sessions = Session::query()
            ->with(['client', 'therapist'])
            ->when($start !== null, fn ($query) => $query->whereDate('date', '>=', $start))
            ->when($end !== null, fn ($query) => $query->whereDate('date', '<=', $end))
            ->get();

        $events = $sessions->map(function (Session $session): array {
            return [
                'id' => $session->id,
                'title' => $session->client?->full_name.' • '.$session->therapist?->full_name,
                'start' => $session->date->format('Y-m-d').'T'.substr((string) $session->time, 0, 5),
                'url' => route('front-desk.sessions.show', $session),
                'backgroundColor' => match ($session->status->value) {
                    SessionStatus::Completed->value => '#16a34a',
                    SessionStatus::Cancelled->value => '#dc2626',
                    default => '#2563eb',
                },
                'borderColor' => 'transparent',
            ];
        })->values();

        return response()->json($events);
    }
}
