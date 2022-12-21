<?php

declare(strict_types=1);

namespace App\Enums;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Locations\Projections\Location;
use App\Domain\Users\Models\User;

enum StructuredDocumentEntityTypeEnum: string
{
    case User = User::class;
    case Location = Location::class;
    case Calendar = CalendarEvent::class;

    public static function asArray(): array
    {
        return array_map(fn (StructuredDocumentEntityTypeEnum $s): string => $s->value, self::cases());
    }
}
