<?php

namespace App\Enums;

enum ModerationStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Qoralama',
            self::PENDING => "Moderatsiyada",
            self::APPROVED => "Tasdiqni",
            self::REJECTED => "Rad etilgan",
            self::BLOCKED => "Bloklangan",
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::BLOCKED => 'red',
        };
    }
}
