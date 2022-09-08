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

            //figure out if any days got removed (ui doesn't currently allow)
            $days_to_remove = $dripCampaign->days->pluck('id')->diff(collect($payload['days'])->pluck('id'));
            $days_to_remove->each(fn ($id) => TrashDripCampaignDay::run($dripCampaign->days->find($id)));

            foreach ($payload['days'] as $day) {
                $dayData['day_of_campaign'] = $day['day_in_campaign'];
                $dayData['email_template_id'] = $day['email'];
                $dayData['sms_template_id'] = $day['sms'];
                $dayData['client_call_script'] = $day['call'];
                if (! array_key_exists('id', $day)) {
                    //new day
                    $dayData['drip_campaign_id'] = $dripCampaign->id;
                    CreateDripCampaignDay::run($dayData);

                    break;
                }
                //not a new day, see if we need to update
                $existingDay = $dripCampaign->days->find($day['id']);
                $existingDayArr = $existingDay->toArray();
                foreach ($dayData as $key => $val) {
                    if ($existingDayArr[$key] !== $val) {
                        //value change detected, update
                        UpdateDripCampaignDay::run($existingDay, $dayData);

                        break;
                    }
                }
            }
        }

        return $dripCampaign->refresh();
    }

    public function rules(): array
    {
        return [
            'is_published' => ['sometimes', 'boolean'],
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['required', 'max:50'],
            'audience_id' => ['required', 'exists:audiences,id'],
            'start_at' => ['sometimes', 'nullable', 'after:now'],
            'end_at' => ['sometimes', 'nullable', 'after:start_at'],
            'completed_at' => ['sometimes', 'nullable', 'after:start_at'],
            'days' => ['array', 'min:1'],
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

        return Redirect::route('mass-comms.drip-campaigns.edit', $dripCampaign->id);
    }
}
