<?php

namespace App\Enums;

enum RequestStatus: string
{
    case NEW = 'new';
    case ACCEPTED = 'accepted';
    case ALTERNATIVE_PROPOSED = 'alternative_proposed';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Yangi',
            self::ACCEPTED => "Qabul qilingan",
            self::ALTERNATIVE_PROPOSED => "Boshqa taklif",
            self::REJECTED => "Rad etilgan",
            self::CANCELLED => "Bekor qilingan",
            self::COMPLETED => "Yakunlangan",
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'blue',
            self::ACCEPTED => 'green',
            self::ALTERNATIVE_PROPOSED => 'yellow',
            self::REJECTED => 'red',
            self::CANCELLED => 'gray',
            self::COMPLETED => 'green',
        };
    }
}
