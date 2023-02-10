<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns\Events;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\StorableEvents\EntityCreated;

class DripCampaignCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return DripCampaign::class;
    }
}
