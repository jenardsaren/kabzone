<?php

namespace App\Policies;

use App\Enums\SessionStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->can('view', $task->session);
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::Therapist)
            && $task->session->therapist_id === $user->id
            && $task->session->status->value === SessionStatus::Pending->value;
    }

    public function updateStatus(User $user, Task $task): bool
    {
        if ($user->isRole(UserRole::Admin)) {
            return true;
        }

        return $user->isRole(UserRole::Assistant)
            && $task->session->assistant_id === $user->id
            && $task->session->status->value === SessionStatus::Pending->value;
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
