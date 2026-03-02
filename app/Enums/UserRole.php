<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Therapist = 'therapist';
    case Assistant = 'assistant';
    case FrontDesk = 'front_desk';
    case Client = 'client';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $role): string => $role->value, self::cases());
    }
}
