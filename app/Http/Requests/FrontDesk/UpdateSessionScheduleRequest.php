<?php

namespace App\Http\Requests\FrontDesk;

use App\Enums\SessionStatus;
use App\Enums\SessionType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Session;
use App\Models\User;
use App\Services\SessionSchedulerService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSessionScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $session = $this->route('session');

        if (! $session instanceof Session) {
            return false;
        }

        return $this->user()?->can('updateSchedule', $session) ?? false;
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
            'description' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Session $session */
            $session = $this->route('session');
            $scheduler = app(SessionSchedulerService::class);
            $client = User::query()->find((int) $this->integer('client_id'));
            $therapist = User::query()->find((int) $this->integer('therapist_id'));

            if (! $client instanceof User || ! $client->isRole(UserRole::Client) || $client->status !== UserStatus::Active) {
                $validator->errors()->add('client_id', 'The selected client must be an active client account.');
            }

            if (! $therapist instanceof User || ! $therapist->isRole(UserRole::Therapist) || $therapist->status !== UserStatus::Active) {
                $validator->errors()->add('therapist_id', 'The selected therapist must be an active therapist account.');
            }

            $date = CarbonImmutable::parse($this->string('date')->toString(), SessionSchedulerService::TIMEZONE);
            $time = $this->string('time')->toString();
            $normalizedTime = strlen($time) === 5 ? $time.':00' : $time;

            if (! $scheduler->isWithinOperatingHours($date, $time)) {
                $validator->errors()->add('time', 'The selected date and time are outside operating hours.');
            }

            $hasConflict = Session::query()
                ->whereKeyNot($session->id)
                ->whereDate('date', $date->toDateString())
                ->where('time', $normalizedTime)
                ->where('status', '!=', SessionStatus::Cancelled->value)
                ->where(function ($query): void {
                    $query->where('therapist_id', (int) $this->integer('therapist_id'))
                        ->orWhere('client_id', (int) $this->integer('client_id'));
                })
                ->exists();

            if ($hasConflict) {
                $validator->errors()->add('time', 'The selected date and time conflict with an existing session.');
            }
        });
    }
}
