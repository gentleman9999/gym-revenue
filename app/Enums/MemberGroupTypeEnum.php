<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberGroupTypeEnum: string
{
    case FAMILY    = 'Family';
    case CORPORATE = 'Corporate';
}
