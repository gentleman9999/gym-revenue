<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaignDay;
use App\Domain\Campaigns\DripCampaigns\DripCampaignDayAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateDripCampaignDay
{
    use AsAction;

    public function handle(DripCampaignDay $dripCampaignDay, array $payload): DripCampaignDay
    {
        DripCampaignDayAggregate::retrieve($dripCampaignDay->id)->update($payload)->persist();

        return $dripCampaignDay->refresh();
    }

    public function rules(): array
    {
        return [
            'drip_campaign_id' => ['required', 'max:50'],
            'day_of_campaign' => ['required'],
            'email_template_id' => ['sometimes', 'nullable'],
            'sms_template_id' => ['sometimes', 'nullable'],
            'call_template_id' => ['sometimes', 'nullable'],
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

    public function asController(ActionRequest $request, DripCampaignDay $dripCampaignDay): DripCampaignDay
    {
        return $this->handle(
            $dripCampaignDay,
            $request->validated()
        );
    }

    public function htmlResponse(DripCampaignDay $dripCampaignDays): RedirectResponse
    {
        Alert::success("Drip Campaign Days '{$dripCampaignDays->dayOfCampaign}' was updated")->flash();

        return Redirect::route('mass-comms.drip-campaigns.edit', $dripCampaignDays->id);
    }
}
