<?php

use App\Enums\UserRole;
use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->beforeEach(function (): void {
        $this->withoutVite();
    })
    ->in('Feature', 'Unit');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function makeUser(UserRole $role, array $attributes = []): User
{
    if ($role === UserRole::Client) {
        return User::factory()->client()->create(array_merge([
            'role' => $role,
        ], $attributes));
    }

    return User::factory()->staff($role->value)->create(array_merge([
        'role' => $role,
        'parent_name' => null,
    ], $attributes));
}

function signInAs(UserRole $role, array $attributes = []): User
{
    $user = makeUser($role, $attributes);

    test()->actingAs($user);

    return $user;
}
