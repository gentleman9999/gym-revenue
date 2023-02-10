<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class UnpublishDripCampaign
{
    use AsAction;

    public string $commandSignature   = 'drip-campaign:unpublish {id}';
    public string $commandDescription = 'unpublishes the drip campaign';

    public function handle(DripCampaign $dripCampaign): void
    {
        DripCampaignAggregate::retrieve($dripCampaign->id)->unpublish()->persist();
    }

    public function asCommand(Command $command): void
    {
        $dripCampaign = DripCampaign::findOrFail($command->argument('id'));
        $dripCampaign = $this->handle($dripCampaign);
        $command->info('Unpublished Campaign ' . $dripCampaign->name);
    }
}
