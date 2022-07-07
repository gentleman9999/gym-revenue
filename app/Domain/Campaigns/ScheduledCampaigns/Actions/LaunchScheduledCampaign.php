<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class LaunchScheduledCampaign
{
    use AsAction;

    public string $commandSignature = 'scheduled-campaign:launch {id}';
    public string $commandDescription = 'launches the scheduled campaign';

    public function handle(ScheduledCampaign $scheduledCampaign): void
    {
        ScheduledCampaignAggregate::retrieve($scheduledCampaign->id)->launch()->persist();
    }

    public function asCommand(Command $command): void
    {
        $scheduledCampaign = ScheduledCampaign::findOrFail($command->argument('id'));
        $scheduledCampaign = $this->handle($scheduledCampaign);
        $command->info('Launched Campaign ' . $scheduledCampaign->name);
    }
}
