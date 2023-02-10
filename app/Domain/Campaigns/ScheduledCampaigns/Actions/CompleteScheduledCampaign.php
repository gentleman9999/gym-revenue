<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class CompleteScheduledCampaign
{
    use AsAction;

    public function handle(ScheduledCampaign $scheduledCampaign): void
    {
        ScheduledCampaignAggregate::retrieve($scheduledCampaign->id)->complete()->persist();
    }
}
