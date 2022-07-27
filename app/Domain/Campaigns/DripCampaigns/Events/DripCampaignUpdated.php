<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityUpdated;

class DripCampaignUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return DripCampaign::class;
    }
}
