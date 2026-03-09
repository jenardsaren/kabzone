<?php

namespace App\Http\Requests\FrontDesk;

use App\Enums\SessionType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Session;
use App\Models\User;
use App\Services\SessionSchedulerService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Session::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('assistant_id') === '') {
            $this->merge([
                'assistant_id' => null,
            ]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'type' => ['required', Rule::in(SessionType::values())],
            'client_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'therapist_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'assistant_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'description' => ['nullable', 'string'],
            'payment_status' => ['required', Rule::in(['Paid', 'Unpaid'])],
            'schedule_mode' => ['required', Rule::in(['single', 'repeat', 'repeat_weekly'])],
            'repeat_days' => [
                'nullable',
                'integer',
                'min:1',
                Rule::requiredIf($this->input('schedule_mode') !== 'single'),
                Rule::when(
                    $this->input('schedule_mode') === 'repeat_weekly',
                    ['max:12'],
                    ['max:30']
                ),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $scheduler = app(SessionSchedulerService::class);
            $client = User::query()->find((int) $this->integer('client_id'));
            $therapist = User::query()->find((int) $this->integer('therapist_id'));

            if (! $client instanceof User || ! $client->isRole(UserRole::Client) || $client->status !== UserStatus::Active) {
                $validator->errors()->add('client_id', 'The selected client must be an active client account.');
            }

            if (! $therapist instanceof User || ! $therapist->isRole(UserRole::Therapist) || $therapist->status !== UserStatus::Active) {
                $validator->errors()->add('therapist_id', 'The selected therapist must be an active therapist account.');
            }

            if ($this->input('assistant_id') !== null) {
                $assistant = User::query()->find((int) $this->integer('assistant_id'));

                if (! $assistant instanceof User || ! $assistant->isRole(UserRole::Assistant) || $assistant->status !== UserStatus::Active) {
                    $validator->errors()->add('assistant_id', 'The selected assistant must be an active assistant account.');
                }
            }

            $date = CarbonImmutable::parse($this->string('date')->toString(), SessionSchedulerService::TIMEZONE);
            $time = $this->string('time')->toString();

            if (! $scheduler->isWithinOperatingHours($date, $time)) {
                $validator->errors()->add('time', 'The selected date and time are outside operating hours.');
            }

            $assistantId = $this->input('assistant_id');
            $therapistId = (int) $this->integer('therapist_id');
            $clientId = (int) $this->integer('client_id');

            if ($this->string('schedule_mode')->toString() === 'single') {
                if ($assistantId !== null
                    && $scheduler->hasAssistantConflict($date, $time, (int) $assistantId)) {
                    $validator->errors()->add('time', 'The selected assistant is already booked for that time.');
                } elseif ($scheduler->hasClientTherapistConflict($date, $time, $therapistId, $clientId)) {
                    $validator->errors()->add('time', 'The selected client already has a session with this therapist at that time.');
                }
            }
        });
    }
}
