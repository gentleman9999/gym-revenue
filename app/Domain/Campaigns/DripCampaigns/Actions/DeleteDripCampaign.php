<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteDripCampaign
{
    use AsAction;

    public string $commandSignature   = 'drip-campaign:delete {id}';
    public string $commandDescription = 'Permanently deletes the DripCampaign';

    public function handle(DripCampaign $dripCampaign): DripCampaign
    {
        DripCampaignAggregate::retrieve($dripCampaign->id)->delete()->persist();

        return $dripCampaign;
    }

    public function asCommand(Command $command): void
    {
        $dripCampaign = DripCampaign::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete DripCampaign '{$dripCampaign->name}'? This cannot be undone")) {
            $dripCampaign = $this->handle($dripCampaign);
            $command->info('Deleted audience ' . $dripCampaign->name);

            return;
        }
        $command->info('Aborted deleting audience ' . $dripCampaign->name);
    }
}
