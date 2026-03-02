<?php

namespace Database\Factories;

use App\Enums\SessionStatus;
use App\Enums\SessionType;
use App\Enums\UserRole;
use App\Models\Session;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = CarbonImmutable::now('Asia/Manila')->addDays(fake()->numberBetween(0, 7));
        $hour = fake()->numberBetween(8, 17);

        return [
            'date' => $date->toDateString(),
            'time' => sprintf('%02d:00:00', $hour),
            'type' => fake()->randomElement(SessionType::values()),
            'client_id' => User::factory()->client(),
            'therapist_id' => User::factory()->staff(UserRole::Therapist->value),
            'assistant_id' => null,
            'description' => fake()->sentence(),
            'notes' => null,
            'status' => SessionStatus::Pending,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => SessionStatus::Completed,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => SessionStatus::Cancelled,
        ]);
    }
}
