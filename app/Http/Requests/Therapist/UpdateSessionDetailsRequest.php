<?php

namespace App\Http\Requests\Therapist;

use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSessionDetailsRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
