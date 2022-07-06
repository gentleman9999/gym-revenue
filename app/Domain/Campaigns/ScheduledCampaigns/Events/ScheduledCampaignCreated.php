<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\EntityCreated;

class ScheduledCampaignCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return ScheduledCampaign::class;
    }
}
