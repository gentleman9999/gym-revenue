<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class CompleteDripCampaign
{
    use AsAction;

    public function handle(DripCampaign $dripCampaign): void
    {
        DripCampaignAggregate::retrieve($dripCampaign->id)->complete()->persist();
    }
}
