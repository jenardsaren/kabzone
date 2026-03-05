<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateOfBirth = CarbonImmutable::instance(fake()->dateTimeBetween('-90 years', '-5 years'));

        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->address(),
            'contact_no' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(Gender::values()),
            'date_of_birth' => $dateOfBirth->toDateString(),
            'age' => CarbonImmutable::now()->diffInYears($dateOfBirth),
            'status' => UserStatus::Active,
            'parent_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Client,
            'must_change_password' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function client(): static
    {
        return $this->state(fn (): array => [
            'role' => UserRole::Client,
            'status' => UserStatus::Active,
            'parent_name' => fake()->name(),
        ]);
    }

    public function staff(string $role = UserRole::Therapist->value): static
    {
        if (! in_array($role, [
            UserRole::Admin->value,
            UserRole::Therapist->value,
            UserRole::Assistant->value,
            UserRole::FrontDesk->value,
        ], true)) {
            throw new InvalidArgumentException('Staff role must be admin, therapist, assistant, or front_desk.');
        }

        return $this->state(fn (): array => [
            'role' => $role,
            'status' => UserStatus::Active,
            'parent_name' => null,
        ]);
    }

    public function mustChangePassword(): static
    {
        return $this->state(fn (): array => [
            'must_change_password' => true,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => [
            'email_verified_at' => null,
        ]);
    }
}
