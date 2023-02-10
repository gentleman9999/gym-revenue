<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteScheduledCampaign
{
    use AsAction;

    public string $commandSignature   = 'scheduled-campaign:delete {id}';
    public string $commandDescription = 'Permanently deletes the ScheduledCampaign';

    public function handle(ScheduledCampaign $scheduledCampaign): ScheduledCampaign
    {
        ScheduledCampaignAggregate::retrieve($scheduledCampaign->id)->delete()->persist();

        return $scheduledCampaign;
    }

    public function asCommand(Command $command): void
    {
        $scheduledCampaign = ScheduledCampaign::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete ScheduledCampaign '{$scheduledCampaign->name}'? This cannot be undone")) {
            $scheduledCampaign = $this->handle($scheduledCampaign);
            $command->info('Deleted audience ' . $scheduledCampaign->name);

            return;
        }
        $command->info('Aborted deleting audience ' . $scheduledCampaign->name);
    }
}
