<?php

namespace App\Http\Controllers\Therapist;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Therapist\StoreTaskRequest;
use App\Http\Requests\Therapist\UpdateSessionDetailsRequest;
use App\Http\Requests\Therapist\UpdateSessionStatusRequest;
use App\Models\Session;
use App\Models\Task;
use App\Services\SessionLockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function show(Session $session): View
    {
        $this->authorize('view', $session);

        $session->load(['client', 'therapist', 'assistant', 'tasks', 'note']);

        return view('therapist.sessions.show', [
            'session' => $session,
        ]);
    }

    public function updateDetails(
        UpdateSessionDetailsRequest $request,
        Session $session,
        SessionLockService $sessionLockService
    ): RedirectResponse {
        $sessionLockService->ensureEditable($session);

        $session->update($request->validated());

        return redirect()
            ->route('therapist.sessions.show', $session)
            ->with('status', 'session-details-updated');
    }

    public function storeTask(
        StoreTaskRequest $request,
        Session $session,
        SessionLockService $sessionLockService
    ): RedirectResponse {
        $sessionLockService->ensureEditable($session);

        $session->tasks()->create([
            ...$request->validated(),
            'status' => TaskStatus::Pending,
        ]);

        return redirect()
            ->route('therapist.sessions.show', $session)
            ->with('status', 'task-created');
    }

    public function updateTask(
        StoreTaskRequest $request,
        Session $session,
        Task $task,
        SessionLockService $sessionLockService
    ): RedirectResponse {
        if ($task->session_id !== $session->id) {
            abort(404);
        }

        $sessionLockService->ensureEditable($session);

        $task->update($request->validated());

        return redirect()
            ->route('therapist.sessions.show', $session)
            ->with('status', 'task-updated');
    }

    public function updateStatus(UpdateSessionStatusRequest $request, Session $session): RedirectResponse
    {
        $session->update([
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('therapist.sessions.show', $session)
            ->with('status', 'session-status-updated');
    }
}
