<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OverrideSessionRequest;
use App\Http\Requests\FrontDesk\StoreSessionRequest;
use App\Models\Session;
use App\Models\User;
use App\Services\SessionSchedulerService;
use Illuminate\Http\RedirectResponse;
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

        return view('admin.sessions.create', [
            'clients' => $clients,
            'therapists' => $therapists,
        ]);
    }

    public function store(StoreSessionRequest $request, SessionSchedulerService $schedulerService): RedirectResponse
    {
        $result = $schedulerService->schedule($request->validated());

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'session-scheduled')
            ->with('scheduled_count', $result['created']->count())
            ->with('skipped_dates', $result['skipped']);
    }

    public function show(Session $session): View
    {
        $this->authorize('view', $session);

        $session->load(['client', 'therapist', 'assistant', 'tasks', 'note']);

        return view('admin.sessions.show', [
            'session' => $session,
        ]);
    }

    public function edit(Session $session): View
    {
        $this->authorize('override', $session);

        $session->load(['client', 'therapist', 'assistant', 'tasks']);

        $assistants = User::query()
            ->where('role', UserRole::Assistant->value)
            ->where('status', UserStatus::Active->value)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.sessions.edit', [
            'session' => $session,
            'assistants' => $assistants,
        ]);
    }

    public function update(OverrideSessionRequest $request, Session $session): RedirectResponse
    {
        $session->update($request->validated());

        return redirect()
            ->route('admin.sessions.edit', $session)
            ->with('status', 'session-overridden');
    }
}
