<?php

declare(strict_types=1);

namespace App\Enums;

enum GenderEnum: string
{
    case GENDER_MALE = 'male';
    case GENDER_FEMALE = 'female';
    case GENDER_OTHER = 'other';
}
