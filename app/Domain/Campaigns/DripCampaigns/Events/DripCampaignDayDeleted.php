<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\StorableEvents\EntityDeleted;

class DripCampaignDayDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return DripCampaignDay::class;
    }
}
