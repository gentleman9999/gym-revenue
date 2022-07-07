<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityDeleted;

class DripCampaignDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return DripCampaign::class;
    }
}
