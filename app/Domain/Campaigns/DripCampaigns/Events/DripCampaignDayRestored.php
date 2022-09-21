<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\StorableEvents\EntityRestored;

class DripCampaignDayRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return DripCampaignDay::class;
    }
}
