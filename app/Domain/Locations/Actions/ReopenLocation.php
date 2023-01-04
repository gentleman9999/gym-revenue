<?php

namespace App\Domain\Locations\Actions;

use App\Domain\Locations\LocationAggregate;
use App\Domain\Locations\Projections\Location;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class ReopenLocation
{
    use AsAction;

    public function handle(Location $location): Location
    {
        LocationAggregate::retrieve($location->id)->reopen()->persist();

        return $location->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.restore', Location::class);
    }

    public function asController(Location $location): Location
    {
        return $this->handle(
            $location,
        );
    }

    public function htmlResponse(Location $location): RedirectResponse
    {
        Alert::success("Location '{$location->name}' was reopened")->flash();

        return Redirect::back();
    }
}
