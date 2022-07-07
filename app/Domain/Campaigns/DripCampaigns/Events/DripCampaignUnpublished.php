<?php

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\GymRevCrudEvent;

class DripCampaignUnpublished extends GymRevCrudEvent
{
    protected function getEntity(): string
    {
        return DripCampaign::class;
    }

    protected function getOperation(): string
    {
        return "UNPUBLISHED";
    }
}
