<?php

namespace App\Domain\Campaigns\ScheduledCampaigns\Actions;

use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaignAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateScheduledCampaign
{
    use AsAction;

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
            'name' => ['sometimes', 'max:50'],
            'audience_id' => ['sometimes', 'exists:audiences,id'],
            'send_at' => ['sometimes', 'after:now'],
            'template_type' => ['sometimes', 'string'],
            'template_id' => ['sometimes', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
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

        return Redirect::route('comms.scheduled-campaigns.edit', $scheduledCampaign->id);
    }
}