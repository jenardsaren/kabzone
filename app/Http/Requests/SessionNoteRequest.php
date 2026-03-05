<?php

namespace App\Http\Requests;

use App\Models\Note;
use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SessionNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $session = $this->route('session');

        if (! $session instanceof Session) {
            return false;
        }

        return $this->user()?->can('updateTherapistFields', $session) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'note_section' => ['required', Rule::in(['behavior', 'activities', 'ef'])],
            'bo_other_details' => ['nullable', 'string', 'max:500'],
            'am_activities_and_management' => ['nullable', 'string', 'max:500'],
        ];

        foreach (Note::BEHAVIOR_FIELDS as $field) {
            $rules[$field] = ['sometimes', 'boolean'];
        }

        foreach (Note::EF_BOOLEAN_FIELDS as $field) {
            $rules[$field] = ['sometimes', 'boolean'];
        }

        foreach (Note::EF_TEXT_FIELDS as $field) {
            $rules[$field] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }
}
