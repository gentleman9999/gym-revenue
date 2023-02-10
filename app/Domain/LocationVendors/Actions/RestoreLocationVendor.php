<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Actions;

use App\Domain\LocationVendors\LocationVendorAggregate;
use App\Domain\LocationVendors\Projections\LocationVendor;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreLocationVendor
{
    use AsAction;

    public function handle(LocationVendor $location_vendor): LocationVendor
    {
        LocationVendorAggregate::retrieve($location_vendor->id)->restore()->persist();

        return $location_vendor->refresh();
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locationVendors.restore', LocationVendor::class);
    }

    public function asController(Request $request, LocationVendor $location_vendor): LocationVendor
    {
        return $this->handle(
            $location_vendor,
        );
    }

    public function htmlResponse(LocationVendor $location_vendor): RedirectResponse
    {
        Alert::success("Vendor '{$location_vendor->poc_name}' was restored")->flash();

        return Redirect::back();
    }
}
