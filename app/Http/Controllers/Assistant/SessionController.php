<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assistant\UpdateTaskStatusRequest;
use App\Models\Session;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function show(Session $session): View
    {
        $this->authorize('view', $session);

        $session->load(['client', 'therapist', 'assistant', 'tasks']);

        return view('assistant.sessions.show', [
            'session' => $session,
        ]);
    }

    public function updateTaskStatus(UpdateTaskStatusRequest $request, Task $task): RedirectResponse
    {
        $task->update([
            'status' => $request->validated('status'),
        ]);

        return back()->with('status', 'task-status-updated');
    }
}
