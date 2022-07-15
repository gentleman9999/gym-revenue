<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityTrashed;

class DripCampaignTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return DripCampaign::class;
    }
}
