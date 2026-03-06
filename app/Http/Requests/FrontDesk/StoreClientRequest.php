<?php

namespace App\Http\Requests\FrontDesk;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('createClient', User::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'contact_no' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(Gender::values())],
            'date_of_birth' => ['required', 'date'],
            'parent_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'parent_name.required' => 'Parent name is required for client accounts.',
        ];
    }
}
