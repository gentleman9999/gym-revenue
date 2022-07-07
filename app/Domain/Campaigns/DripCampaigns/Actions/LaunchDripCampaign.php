<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class LaunchDripCampaign
{
    use AsAction;

    public string $commandSignature = 'drip-campaign:launch {id}';
    public string $commandDescription = 'launches the drip campaign';

    public function handle(DripCampaign $dripCampaign): void
    {
        DripCampaignAggregate::retrieve($dripCampaign->id)->launch()->persist();
    }

    public function asCommand(Command $command): void
    {
        $dripCampaign = DripCampaign::findOrFail($command->argument('id'));
        $this->handle($dripCampaign);
        $command->info('Launched Campaign ' . $dripCampaign->name);
    }
}
