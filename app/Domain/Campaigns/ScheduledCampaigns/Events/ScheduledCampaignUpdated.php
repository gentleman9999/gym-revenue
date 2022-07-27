<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\EntityUpdated;

class ScheduledCampaignUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return ScheduledCampaign::class;
    }
}
