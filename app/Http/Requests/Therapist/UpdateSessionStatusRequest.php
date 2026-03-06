<?php

namespace App\Http\Requests\Therapist;

use App\Enums\SessionStatus;
use App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $session = $this->route('session');

        if (! $session instanceof Session) {
            return false;
        }

        return $this->user()?->can('changeStatus', $session) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                SessionStatus::Completed->value,
                SessionStatus::Cancelled->value,
            ])],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Session $session */
            $session = $this->route('session');

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if ($session->status->value !== SessionStatus::Pending->value) {
                $validator->errors()->add('status', 'Only pending sessions can transition to completed or cancelled.');
            }

            if ($this->string('status')->toString() === SessionStatus::Completed->value) {
                if ($session->assistant_id === null) {
                    $validator->errors()->add('status', 'An assistant must be assigned before completing this session.');
                }
            }
        });
    }
}
