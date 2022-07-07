<?php

namespace App\Domain\Campaigns\DripCampaigns\Actions;

use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\DripCampaigns\DripCampaignAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateDripCampaign
{
    use AsAction;

    public function handle(DripCampaign $dripCampaign, array $payload): DripCampaign
    {
        if (! $dripCampaign->can_publish) {
            //campaign is either active or completed. don't allow updating anything but name
            $allowedKeys = ['name'];
            $payload = array_filter_only_keys($payload, $allowedKeys);
        }

        if (count($payload)) {
            DripCampaignAggregate::retrieve($dripCampaign->id)->update($payload)->persist();
        }

        return $dripCampaign->refresh();
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'max:50'],
            'audience_id' => ['sometimes', 'exists:audiences,id'],
            'start_at' => ['sometimes', 'nullable', 'after:now'],
            'end_at' => ['sometimes', 'nullable', 'after:start_at'],
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

        return $current_user->can('drip-campaigns.create', DripCampaign::class);
    }

    public function asController(ActionRequest $request, DripCampaign $dripCampaign): DripCampaign
    {
        return $this->handle(
            $dripCampaign,
            $request->validated()
        );
    }

    public function htmlResponse(DripCampaign $dripCampaign): RedirectResponse
    {
        Alert::success("Drip Campaign '{$dripCampaign->name}' was updated")->flash();

        return Redirect::route('comms.drip-campaigns.edit', $dripCampaign->id);
    }
}
