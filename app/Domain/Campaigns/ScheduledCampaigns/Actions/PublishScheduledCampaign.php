<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class PublishScheduledCampaign
{
    use AsAction;

    public string $commandSignature   = 'scheduled-campaign:publish {id}';
    public string $commandDescription = 'publishes the scheduled campaign';

    public function handle(ScheduledCampaign $scheduledCampaign): void
    {
        ScheduledCampaignAggregate::retrieve($scheduledCampaign->id)->publish()->persist();
    }

    public function asCommand(Command $command): void
    {
        $scheduledCampaign = ScheduledCampaign::findOrFail($command->argument('id'));
        $scheduledCampaign = $this->handle($scheduledCampaign);
        $command->info('Published Campaign ' . $scheduledCampaign->name);
    }
}
