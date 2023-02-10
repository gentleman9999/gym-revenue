<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityDeleted;

class DripCampaignDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return DripCampaign::class;
    }
}
