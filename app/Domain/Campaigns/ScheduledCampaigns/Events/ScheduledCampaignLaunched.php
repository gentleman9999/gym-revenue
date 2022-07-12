<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\GymRevCrudEvent;

class ScheduledCampaignLaunched extends GymRevCrudEvent
{
    protected function getEntity(): string
    {
        return ScheduledCampaign::class;
    }

    protected function getOperation(): string
    {
        return "LAUNCHED";
    }
}
