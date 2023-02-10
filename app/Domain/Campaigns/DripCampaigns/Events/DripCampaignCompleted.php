<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\GymRevCrudEvent;

class DripCampaignCompleted extends GymRevCrudEvent
{
    public function getEntity(): string
    {
        return DripCampaign::class;
    }

    protected function getOperation(): string
    {
        return "CREATED";
    }
}
