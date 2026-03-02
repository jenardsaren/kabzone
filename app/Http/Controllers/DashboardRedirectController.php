<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->isRole(UserRole::Admin)) {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->isRole(UserRole::FrontDesk)) {
            return redirect()->route('front-desk.dashboard');
        }

        if ($user?->isRole(UserRole::Therapist)) {
            return redirect()->route('therapist.dashboard');
        }

        if ($user?->isRole(UserRole::Assistant)) {
            return redirect()->route('assistant.dashboard');
        }

        return redirect()->route('client.dashboard');
    }
}
