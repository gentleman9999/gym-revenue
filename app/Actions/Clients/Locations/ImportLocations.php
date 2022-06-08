<?php

namespace App\Actions\Clients\Locations;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class ImportLocations
{
    use AsAction;

    public function handle($data, $current_user = null)
    {
        $result = false;
        foreach ($data as $item) {
            if ($item['extension'] === 'csv') {
                ClientAggregate::retrieve($item['client_id'])->importLocations($current_user->id ?? "Auto Generated", $item['key'])->persist();
                $result = true;
            } else {
                Alert::error("File name: ".$item['filename']. " doesn't meet extension requirements of '.csv'.")->flash();
            }
        }

        return $result;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.create', Location::class);
    }

    public function asController(ActionRequest $request)
    {
        $location = $this->handle(
            $request->all(),
            $request->user(),
        );

        if ($location) { //If at-least one file was correct format and imported we display success
            Alert::success("Location Import complete.")->flash();
        }

        return Redirect::route('locations');
    }
}