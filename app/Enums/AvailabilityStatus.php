<?php

namespace App\Enums;

enum AvailabilityStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case SOLD = 'sold';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Mavjud',
            self::RENTED => 'Ijaraga berilgan',
            self::SOLD => 'Sotilgan',
            self::WITHDRAWN => 'Olib taslangan',
        };
    }
}
