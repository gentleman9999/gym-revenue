<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityCreated;

class DripCampaignCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return DripCampaign::class;
    }
}
