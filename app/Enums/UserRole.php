<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case MANAGER = 'manager';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
