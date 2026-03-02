<?php

namespace App\Http\Requests\Admin;

use App\Enums\SessionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OverrideSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $session = $this->route('session');

        if (! $session instanceof Session) {
            return false;
        }

        return $this->user()?->can('override', $session) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'assistant_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(SessionStatus::values())],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty() || $this->input('assistant_id') === null) {
                return;
            }

            $assistant = User::query()->find((int) $this->integer('assistant_id'));

            if (! $assistant instanceof User || ! $assistant->isRole(UserRole::Assistant) || $assistant->status !== UserStatus::Active) {
                $validator->errors()->add('assistant_id', 'The selected assistant must be an active assistant account.');
            }
        });
    }
}
