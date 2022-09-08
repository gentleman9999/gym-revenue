<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\StorableEvents\EntityTrashed;

class DripCampaignDayTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return DripCampaignDay::class;
    }
}
