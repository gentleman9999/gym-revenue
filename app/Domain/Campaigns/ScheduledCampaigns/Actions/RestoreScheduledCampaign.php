<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreScheduledCampaign
{
    use AsAction;

    public string $commandSignature = 'scheduled-campaign:restore {id}';
    public string $commandDescription = 'Restores the audience';

    public function handle(string $id): ScheduledCampaign
    {
        ScheduledCampaignAggregate::retrieve($id)->restore()->persist();

        return ScheduledCampaign::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('scheduled-campaigns.create', ScheduledCampaign::class);
    }

    public function asController(ActionRequest $request, ScheduledCampaign $scheduledCampaign): ScheduledCampaign
    {
        return $this->handle(
            $scheduledCampaign->id,
        );
    }

    public function htmlResponse(ScheduledCampaign $scheduledCampaign): RedirectResponse
    {
        Alert::success("Scheduled Campaign '{$scheduledCampaign->name}' was restored")->flash();

        return Redirect::route('comms.scheduled-campaigns');
    }

    public function asCommand(Command $command): void
    {
        $scheduledCampaign = $this->handle($command->argument('id'));
        $command->info('Restored ScheduledCampaign ' . $scheduledCampaign->name);
    }
}