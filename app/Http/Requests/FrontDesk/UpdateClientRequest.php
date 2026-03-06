<?php

namespace App\Http\Requests\FrontDesk;

use App\Enums\Gender;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        $client = $this->route('client');

        if (! $client instanceof User) {
            return false;
        }

        return $this->user()?->can('updateClient', $client) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var User $client */
        $client = $this->route('client');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'contact_no' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(Gender::values())],
            'date_of_birth' => ['required', 'date'],
            'parent_name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(UserStatus::values())],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($client->id)],
        ];
    }
}
