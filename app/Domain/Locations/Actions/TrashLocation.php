<?php

namespace App\Domain\Locations\Actions;

use App\Domain\Locations\LocationAggregate;
use App\Domain\Locations\Projections\Location;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashLocation
{
    use AsAction;

    public function handle(Location $location): Location
    {
        LocationAggregate::retrieve($location->id)->trash()->persist();

        return $location->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.trash', Location::class);
    }

    public function asController(Request $request, Location $location): Location
    {
        return $this->handle(
            $location,
        );
    }

    public function htmlResponse(Location $location): RedirectResponse
    {
        Alert::success("Location '{$location->name}' sent to trash")->flash();

        return Redirect::back();
    }
}
