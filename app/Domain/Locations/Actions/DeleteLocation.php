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

class DeleteLocation
{
    use AsAction;

    public function handle(Location $location): Location
    {
        LocationAggregate::retrieve($location->id)->delete()->persist();

        return $location;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.delete', Location::class);
    }

    public function asController(Request $request, Location $location): Location
    {
        return $this->handle(
            $location
        );
    }

    public function htmlResponse(Location $location): RedirectResponse
    {
        Alert::success("Location '{$location->name}' was deleted")->flash();

        return Redirect::back();
    }
}
