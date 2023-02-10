<?php

declare(strict_types=1);

namespace App\Enums;

enum AgreementAvailabilityEnum: string
{
    case InStore = 'In Store';
    case Online  = 'Online';
    case InApp   = 'In App';

    public static function asArray(): array
    {
        return array_map(fn (AgreementAvailabilityEnum $s): string => $s->value, self::cases());
    }
}
