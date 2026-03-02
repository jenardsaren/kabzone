<?php

namespace App\Services;

use App\Enums\SessionStatus;
use App\Models\Session;
use Illuminate\Validation\ValidationException;

class SessionLockService
{
    public function isLocked(Session $session): bool
    {
        return $session->status->value === SessionStatus::Completed->value;
    }

    /**
     * @throws ValidationException
     */
    public function ensureEditable(Session $session, bool $allowAdminOverride = false): void
    {
        if (! $this->isLocked($session) || $allowAdminOverride) {
            return;
        }

        throw ValidationException::withMessages([
            'session' => 'Completed sessions are locked and can only be edited by admin override.',
        ]);
    }
}
