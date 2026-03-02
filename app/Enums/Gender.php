<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $gender): string => $gender->value, self::cases());
    }
}
