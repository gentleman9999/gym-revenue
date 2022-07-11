<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\EntityTrashed;

class ScheduledCampaignTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return ScheduledCampaign::class;
    }
}
