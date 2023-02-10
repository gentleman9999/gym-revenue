<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Events;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\StorableEvents\EntityDeleted;

class ScheduledCampaignDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return ScheduledCampaign::class;
    }
}
