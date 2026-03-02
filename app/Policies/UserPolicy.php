<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isRole(UserRole::Admin) || $user->isRole(UserRole::FrontDesk);
    }

    public function view(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return true;
        }

        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::FrontDesk) && $target->isRole(UserRole::Client);
    }

    public function createStaff(User $user): bool
    {
        return $user->isRole(UserRole::Admin);
    }

    public function updateStaff(User $user, User $target): bool
    {
        return $user->isRole(UserRole::Admin) && ! $target->isRole(UserRole::Client);
    }

    public function createClient(User $user): bool
    {
        return $user->isRole(UserRole::Admin) || $user->isRole(UserRole::FrontDesk);
    }

    public function updateClient(User $user, User $target): bool
    {
        if (! $target->isRole(UserRole::Client)) {
            return false;
        }

        return $user->isRole(UserRole::Admin) || $user->isRole(UserRole::FrontDesk);
    }
}
