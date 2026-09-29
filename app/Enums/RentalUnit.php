<?php

namespace App\Enums;

enum RentalUnit: string
{
    case WHOLE = 'whole';
    case ROOM = 'room';
    case BED = 'bed';

    public function label(): string
    {
        return match ($this) {
            self::WHOLE => 'Butun kvartira/uy',
            self::ROOM => 'Xona',
            self::BED => "O'rin",
        };
    }
}
