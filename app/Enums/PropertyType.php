<?php

namespace App\Enums;

enum PropertyType: string
{
    case APARTMENT = 'apartment';
    case HOUSE = 'house';
    case DORMITORY = 'dormitory';

    public function label(): string
    {
        return match ($this) {
            self::APARTMENT => 'Kvartira',
            self::HOUSE => 'Uy',
            self::DORMITORY => 'Yotoqxona',
        };
    }
}
