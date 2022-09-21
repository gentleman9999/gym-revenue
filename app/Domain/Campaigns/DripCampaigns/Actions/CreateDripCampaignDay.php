<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\Domain\Campaigns\DripCampaigns\DripCampaignDayAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateDripCampaignDay
{
    use AsAction;

    public function handle(array $payload): DripCampaignDay
    {
        $id = Uuid::new();

        DripCampaignDayAggregate::retrieve($id)->create($payload)->persist();

        return DripCampaignDay::findOrFail($id);
    }

    public function rules(): array
    {
        return [
            'drip_campaign_id' => ['required', 'max:50'],
            'day_of_campaign' => ['required'],
            'email_template_id' => ['sometimes', 'nullable'],
            'sms_template_id' => ['sometimes', 'nullable'],
            'client_call_script' => ['sometimes', 'nullable'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('drip-campaigns.create', DripCampaignDay::class);
    }

    public function asController(ActionRequest $request): DripCampaignDay
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(DripCampaignDay $dripCampaignDays): RedirectResponse
    {
        Alert::success("Drip Campaign Days '{$dripCampaignDays->dayOfCampaign}' was created")->flash();

        return Redirect::route('mass-comms.drip-campaigns.edit', $dripCampaignDays->id);
    }
}
