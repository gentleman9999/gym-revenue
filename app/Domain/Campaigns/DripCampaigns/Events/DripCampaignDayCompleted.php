<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\StorableEvents\GymRevCrudEvent;

class DripCampaignDayCompleted extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return DripCampaignDay::class;
    }

    protected function getOperation(): string
    {
        return "CREATED";
    }
}
