<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityUpdated;

class DripCampaignUpdated extends EntityUpdated
{
    protected function getEntity(): string
    {
        return DripCampaign::class;
    }
}
