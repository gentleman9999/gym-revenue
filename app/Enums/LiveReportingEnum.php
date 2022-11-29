<?php

namespace App\Enums;

enum LiveReportingEnum: string
{
    case ADDED = "added";
    case DELETED = "deleted";
    case CONVERTED = "converted";
    case CONTACTED = "contacted";
    case SCHEDULED_CAMPAIGN_STARTED = "scheduled campaign started";
    case DRIP_CAMPAIGN_STARTED = "drip campaign started";
}
