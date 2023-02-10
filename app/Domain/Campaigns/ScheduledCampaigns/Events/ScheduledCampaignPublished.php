<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\GymRevCrudEvent;

class ScheduledCampaignPublished extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return ScheduledCampaign::class;
    }

    protected function getOperation(): string
    {
        return "PUBLISHED";
    }
}
