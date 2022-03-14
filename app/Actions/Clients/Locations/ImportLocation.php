<?php

namespace App\Actions\Clients\Locations;

use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportLocation
{
    use AsAction;

    public function handle($data, $current_user = null)
    {
        foreach ($data as $item)
        {
                ClientAggregate::retrieve($item['client_id'])->importLocation($current_user->id ?? "Auto Generated", $item['key'])->persist();
        }
        return true;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('locations.create', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {
        $location = $this->handle(
            $request->all(),
            $request->user(),
        );

        Alert::success("CSV imported")->flash();

        return Redirect::route('locations');
    }

}
