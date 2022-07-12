<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\ClientSettingsAggregate;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UploadLogo
{
    use AsAction;

    public function handle($data, $current_user)
    {
        $result = false;
        foreach ($data as $item) {
            $item['client_id'] = $current_user->currentClientId();
            ClientSettingsAggregate::retrieve($current_user->currentClientId())->uploadLogo($item)->persist();
            $result = true;
        }

        return $result;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('client.create', Location::class);
    }

    public function asController(ActionRequest $request)
    {
        $location = $this->handle(
            $request->all(),
            $request->user(),
        );

        if ($location) { //If at-least one file was correct format and imported we display success
            Alert::success("Logo Import complete.")->flash();
        }

        return Redirect::route('settings');
    }
}
