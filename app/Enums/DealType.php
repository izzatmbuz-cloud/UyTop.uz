<?php

namespace App\Enums;

enum DealType: string
{
    case RENT = 'rent';
    case SALE = 'sale';

    public function label(): string
    {
        return match ($this) {
            self::RENT => 'Ijara',
            self::SALE => 'Sotish',
        };
    }
}
