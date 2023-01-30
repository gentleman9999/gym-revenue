<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Attributes\Description;
use BenSampo\Enum\Enum;

abstract class GymRevEnum extends Enum
{
    /**
     * Get the enum as an array formatted for options in a select input.
     *
     * @return array<int, array{value: string, label: string, description?: string}>
     */
    public static function asOptionsArray(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $value) {
            $enum = [];
            $enum['value'] = $value;
            $enum['label'] = static::getFriendlyName($value);
            $enum['description'] = static::getDescription($value);

            $selectArray[] = $enum;
        }

        return $selectArray;
    }
}
