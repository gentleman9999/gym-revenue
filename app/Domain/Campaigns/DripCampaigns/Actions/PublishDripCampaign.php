<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class PublishDripCampaign
{
    use AsAction;

    public string $commandSignature = 'drip-campaign:publish {id}';
    public string $commandDescription = 'publishes the drip campaign';

    public function handle(DripCampaign $dripCampaign): void
    {
        DripCampaignAggregate::retrieve($dripCampaign->id)->publish()->persist();
    }

    public function asCommand(Command $command): void
    {
        $dripCampaign = DripCampaign::findOrFail($command->argument('id'));
        $dripCampaign = $this->handle($dripCampaign);
        $command->info('Published Campaign ' . $dripCampaign->name);
    }
}
