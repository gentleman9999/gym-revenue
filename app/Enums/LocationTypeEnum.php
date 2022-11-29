<?php

declare(strict_types=1);

namespace App\Enums;

enum LocationTypeEnum: string
{
    /** @see resources/js/utils/locationTypeEnum::LOCATION_TYPES */
    case STORE = 'store';
    case OFFICE = 'office';
    case HQ = 'headquarters';

    public static function asArray(): array
    {
        return array_map(fn (LocationTypeEnum $s): array => ['value' => $s->value, 'name' => $s->name], self::cases());
    }
}
