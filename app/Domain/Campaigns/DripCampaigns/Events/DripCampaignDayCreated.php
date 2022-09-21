<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\StorableEvents\EntityCreated;

class DripCampaignDayCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return DripCampaignDay::class;
    }
}
