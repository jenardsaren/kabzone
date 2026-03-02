<?php

namespace App\Http\Requests\Admin;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        $staff = $this->route('staff');

        if (! $staff instanceof User) {
            return false;
        }

        return $this->user()?->can('updateStaff', $staff) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var User $staff */
        $staff = $this->route('staff');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'contact_no' => ['required', 'string', 'max:50'],
            'gender' => ['required', Rule::in(Gender::values())],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($staff->id)],
            'role' => ['required', Rule::in([
                UserRole::Admin->value,
                UserRole::Therapist->value,
                UserRole::Assistant->value,
                UserRole::FrontDesk->value,
            ])],
            'status' => ['required', Rule::in(UserStatus::values())],
        ];
    }
}
