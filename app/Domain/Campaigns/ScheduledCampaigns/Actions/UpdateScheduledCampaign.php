<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Actions\GymRevAction;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateScheduledCampaign extends GymRevAction
{
    public function handle(ScheduledCampaign $scheduledCampaign, array $payload): ScheduledCampaign
    {
//        if(!$scheduledCampaign->can_publish){
//            //campaign is either active or completed. don't allow updating anything but name
//            $allowedKeys = ['name'];
//            $payload = array_only_keep_keys($payload, $allowedKeys);
//        }
        if (count($payload)) {
            ScheduledCampaignAggregate::retrieve($scheduledCampaign->id)->update($payload)->persist();
        }

        return $scheduledCampaign->refresh();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:50'],
            'audience_id' => ['required', 'exists:audiences,id'],
            'send_at' => ['required', 'after:now'],
            'email_template_id' => ['sometimes', 'string'],
            'sms_template_id' => ['sometimes', 'string'],
            'call_template_id' => ['sometimes', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
//            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function mapArgsToHandle($args): array
    {
        $scheduledCampaign = $args['campaign'];

        return [ScheduledCampaign::find($scheduledCampaign['id']), $scheduledCampaign];
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
            $request->validated()
        );
    }

    public function htmlResponse(ScheduledCampaign $scheduledCampaign): RedirectResponse
    {
        Alert::success("Scheduled Campaign '{$scheduledCampaign->name}' was updated")->flash();

        return Redirect::route('mass-comms.scheduled-campaigns.edit', $scheduledCampaign->id);
    }
}
