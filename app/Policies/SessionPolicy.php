<?php

namespace App\Policies;

use App\Enums\SessionStatus;
use App\Enums\UserRole;
use App\Models\Session;
use App\Models\User;

class SessionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->value, [
            UserRole::Admin->value,
            UserRole::FrontDesk->value,
            UserRole::Therapist->value,
            UserRole::Assistant->value,
            UserRole::Client->value,
        ], true);
    }

    public function view(User $user, Session $session): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        if ($user->isRole(UserRole::FrontDesk)) {
            return true;
        }

        if ($user->isRole(UserRole::Therapist)) {
            return $session->therapist_id === $user->id;
        }

        if ($user->isRole(UserRole::Assistant)) {
            return $session->assistant_id === $user->id;
        }

        if ($user->isRole(UserRole::Client)) {
            return $session->client_id === $user->id
                && $session->status->value === SessionStatus::Completed->value;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isRole(UserRole::Admin) || $user->isRole(UserRole::FrontDesk);
    }

    public function updateSchedule(User $user, Session $session): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::FrontDesk)
            && $session->status->value === SessionStatus::Pending->value;
    }

    public function updateTherapistFields(User $user, Session $session): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::Therapist)
            && $session->therapist_id === $user->id
            && $session->status->value === SessionStatus::Pending->value;
    }

    public function assignAssistant(User $user, Session $session): bool
    {
        return $this->updateTherapistFields($user, $session) || $user->isRole(UserRole::Admin);
    }

    public function manageTasks(User $user, Session $session): bool
    {
        return $this->updateTherapistFields($user, $session) || $user->isRole(UserRole::Admin);
    }

    public function changeStatus(User $user, Session $session): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::Therapist)
            && $session->therapist_id === $user->id
            && $session->status->value === SessionStatus::Pending->value;
    }

    public function updateTaskStatus(User $user, Session $session): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::Assistant)
            && $session->assistant_id === $user->id
            && $session->status->value === SessionStatus::Pending->value;
    }

    public function override(User $user, Session $session): bool
    {
        return $user->isRole(UserRole::Admin);
    }
}
