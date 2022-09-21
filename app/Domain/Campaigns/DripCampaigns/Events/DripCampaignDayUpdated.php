<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\StorableEvents\EntityUpdated;

class DripCampaignDayUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return DripCampaignDay::class;
    }
}
