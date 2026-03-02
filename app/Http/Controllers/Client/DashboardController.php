<?php

namespace App\Http\Controllers\Client;

use App\Enums\SessionStatus;
use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $sessions = Session::query()
            ->with(['therapist', 'assistant', 'tasks'])
            ->where('client_id', auth()->id())
            ->where('status', SessionStatus::Completed->value)
            ->recent()
            ->paginate(15);

        return view('client.dashboard', [
            'sessions' => $sessions,
        ]);
    }
}
