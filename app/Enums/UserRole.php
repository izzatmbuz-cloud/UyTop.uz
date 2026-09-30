<?php

namespace App\Enums;

enum UserRole: string
{
    case GUEST = 'guest';
    case USER = 'user';
    case OWNER = 'owner';
    case DEVELOPER = 'developer';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::GUEST => 'Mehmon',
            self::USER => "Ro'yxatdan o'tgan",
            self::OWNER => "E'lon muallifi",
            self::DEVELOPER => 'Quruvchi vakili',
            self::ADMIN => 'Administrator',
        };
    }
}
