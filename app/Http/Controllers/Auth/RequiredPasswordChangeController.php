<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangeRequiredPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RequiredPasswordChangeController extends Controller
{
    public function edit(): View
    {
        return view('auth.required-password-change');
    }

    public function update(ChangeRequiredPasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('status', 'password-changed');
    }
}
