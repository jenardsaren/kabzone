<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionNoteRequest;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;

class SessionNoteController extends Controller
{
    public function update(SessionNoteRequest $request, Session $session): RedirectResponse
    {
        $noteValues = [];
        $section = $request->string('note_section')->toString();
        $otherDetails = $request->input('bo_other_details');
        $noteValues['bo_other_details'] = is_string($otherDetails) ? trim($otherDetails) ?: null : null;

        if ($section === 'behavior') {
            $noteValues = array_merge($noteValues, [
                'bo_cooperative' => $request->has('bo_cooperative') ? true : null,
                'bo_calm_regulated' => $request->has('bo_calm_regulated') ? true : null,
                'bo_restless_fidgety' => $request->has('bo_restless_fidgety') ? true : null,
                'bo_easily_frustrated' => $request->has('bo_easily_frustrated') ? true : null,
                'bo_tantrums' => $request->has('bo_tantrums') ? true : null,
                'bo_meltdowns' => $request->has('bo_meltdowns') ? true : null,
                'bo_avoidant' => $request->has('bo_avoidant') ? true : null,
                'bo_aggressive' => $request->has('bo_aggressive') ? true : null,
                'bo_other' => $request->has('bo_other') ? true : null,
            ]);
        }

        if ($section === 'activities') {
            $activities = $request->input('am_activities_and_management');
            $noteValues['am_activities_and_management'] = is_string($activities) ? trim($activities) ?: null : null;
        }

        $session->note()->updateOrCreate([], $noteValues);

        return redirect()->back()->with('status', 'session-notes-updated');
    }
}
