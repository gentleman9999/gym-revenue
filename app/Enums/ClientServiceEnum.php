<?php

declare(strict_types=1);

namespace App\Enums;

enum ClientServiceEnum: string
{
    case FREE_TRIAL = 'Free Trial';
    case MASS_COMMS = 'Mass Comms';
    case CALENDAR   = 'Calendar';
    case LEADS      = 'Leads';
    case MEMBERS    = 'Members';
    case CUSTOMERS  = 'Customers';
}
