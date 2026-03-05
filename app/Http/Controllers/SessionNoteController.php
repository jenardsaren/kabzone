<?php

namespace App\Http\Controllers;

use App\Http\Requests\SessionNoteRequest;
use App\Models\Note;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;

class SessionNoteController extends Controller
{
    public function update(SessionNoteRequest $request, Session $session): RedirectResponse
    {
        $section = $request->string('note_section')->toString();
        $note = $session->note()->firstOrCreate([]);
        $values = match ($section) {
            'behavior' => $this->behaviorFields($request),
            'activities' => $this->activitiesFields($request),
            'ef' => $this->efFields($request),
            'ei' => $this->eiFields($request),
            'plan' => $this->planFields($request),
            'approval' => $this->approvalFields($request),
            default => [],
        };

        $note->fill($values)->save();

        return redirect()->back()->with('status', 'session-notes-updated');
    }

    private function behaviorFields(SessionNoteRequest $request): array
    {
        $values = [];

        foreach (Note::BEHAVIOR_FIELDS as $field) {
            $values[$field] = $this->checkboxValue($request, $field);
        }

        $values['bo_other_details'] = $this->trimmedText($request->input('bo_other_details'));

        return $values;
    }

    private function activitiesFields(SessionNoteRequest $request): array
    {
        return [
            'am_activities_and_management' => $this->trimmedText($request->input('am_activities_and_management')),
        ];
    }

    private function efFields(SessionNoteRequest $request): array
    {
        $values = [];

        foreach (Note::EF_BOOLEAN_FIELDS as $field) {
            $values[$field] = $this->checkboxValue($request, $field);
        }

        foreach (Note::EF_TEXT_FIELDS as $field) {
            $values[$field] = $this->trimmedText($request->input($field));
        }

        return $values;
    }

    private function eiFields(SessionNoteRequest $request): array
    {
        $values = [];

        foreach (Note::EI_BOOLEAN_FIELDS as $field) {
            $values[$field] = $this->checkboxValue($request, $field);
        }

        foreach (Note::EI_INTEGER_FIELDS as $field) {
            $values[$field] = $this->numericValue($request->input($field));
        }

        foreach (Note::EI_TEXT_FIELDS as $field) {
            $values[$field] = $this->trimmedText($request->input($field));
        }

        return $values;
    }

    private function planFields(SessionNoteRequest $request): array
    {
        $values = [];

        foreach (Note::PLAN_TEXT_FIELDS as $field) {
            $values[$field] = $this->trimmedText($request->input($field));
        }

        return $values;
    }

    private function approvalFields(SessionNoteRequest $request): array
    {
        $values = [];

        foreach (Note::APPROVAL_TEXT_FIELDS as $field) {
            $values[$field] = $this->trimmedText($request->input($field));
        }

        return $values;
    }

    private function checkboxValue(SessionNoteRequest $request, string $field): ?bool
    {
        return $request->has($field) ? true : null;
    }

    private function trimmedText(?string $value): ?string
    {
        return is_string($value) ? trim($value) ?: null : null;
    }

    private function numericValue(?string $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
