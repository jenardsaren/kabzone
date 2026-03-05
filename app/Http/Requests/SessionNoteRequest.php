<?php

namespace App\Http\Requests;

use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'bo_cooperative' => ['sometimes', 'boolean'],
            'bo_calm_regulated' => ['sometimes', 'boolean'],
            'bo_restless_fidgety' => ['sometimes', 'boolean'],
            'bo_easily_frustrated' => ['sometimes', 'boolean'],
            'bo_tantrums' => ['sometimes', 'boolean'],
            'bo_meltdowns' => ['sometimes', 'boolean'],
            'bo_avoidant' => ['sometimes', 'boolean'],
            'bo_aggressive' => ['sometimes', 'boolean'],
            'bo_other' => ['sometimes', 'boolean'],
            'bo_other_details' => ['nullable', 'string', 'max:500'],
            'am_activities_and_management' => ['nullable', 'string', 'max:500'],
        ];
    }
}
