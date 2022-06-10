<?php

namespace App\Enums;

enum ClientServiceEnum: string
{
    case FREE_TRIAL = "FREE_TRIAL";
    case MASS_COMMS = "MASS_COMMS";
    case CALENDAR = "CALENDAR";
    case LEADS = "LEADS";
    case MEMBERS = "MEMBERS";
}
