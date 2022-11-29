<?php

namespace App\Enums;

enum BillingScheduleTypesEnum: string
{
    case PAID_IN_FULL = 'Paid In Full';
    case TERM = 'Term';
}
