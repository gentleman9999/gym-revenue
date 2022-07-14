<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\EntityRestored;

class ScheduledCampaignRestored extends EntityRestored
{
    protected function getEntity(): string
    {
        return ScheduledCampaign::class;
    }
}
