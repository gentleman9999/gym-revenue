<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashScheduledCampaign
{
    use AsAction;

    public string $commandSignature   = 'scheduled-campaign:trash {id}';
    public string $commandDescription = 'Soft deletes the audience';

    public function handle(ScheduledCampaign $scheduledCampaign): ScheduledCampaign
    {
        ScheduledCampaignAggregate::retrieve($scheduledCampaign->id)->trash()->persist();

        return $scheduledCampaign->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('scheduled-campaigns.create', ScheduledCampaign::class);
    }

    public function asController(ActionRequest $request, ScheduledCampaign $scheduledCampaign): ScheduledCampaign
    {
        return $this->handle(
            $scheduledCampaign,
        );
    }

    public function htmlResponse(ScheduledCampaign $scheduledCampaign): RedirectResponse
    {
        Alert::success("Scheduled Campaign '{$scheduledCampaign->name}' was sent to trash")->flash();

        return Redirect::route('mass-comms.scheduled-campaigns');
    }

    public function asCommand(Command $command): void
    {
        $scheduledCampaign = $this->handle($command->argument('id'));
        $command->info('Soft Deleted audience ' . $scheduledCampaign->name);
    }
}
