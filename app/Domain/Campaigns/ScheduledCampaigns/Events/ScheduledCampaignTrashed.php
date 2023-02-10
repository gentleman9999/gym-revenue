<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\EntityTrashed;

class ScheduledCampaignTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return ScheduledCampaign::class;
    }
}
