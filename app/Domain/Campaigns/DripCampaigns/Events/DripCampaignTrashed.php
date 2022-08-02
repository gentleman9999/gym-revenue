<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityTrashed;

class DripCampaignTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return DripCampaign::class;
    }
}
