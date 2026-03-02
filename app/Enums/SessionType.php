<?php

namespace App\Enums;

enum SessionType: string
{
    case Initial = 'initial';
    case Regular = 'regular';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type): string => $type->value, self::cases());
    }
}
