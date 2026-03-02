<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function show(Session $session): View
    {
        $this->authorize('view', $session);

        $session->load(['client', 'therapist', 'assistant', 'tasks']);

        return view('client.sessions.show', [
            'session' => $session,
        ]);
    }
}
