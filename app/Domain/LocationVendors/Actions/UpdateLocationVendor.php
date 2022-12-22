<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Actions;

use App\Domain\LocationVendors\LocationVendorAggregate;
use App\Domain\LocationVendors\Projections\LocationVendor;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLocationVendor
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'max:50'],
            'client_id' => ['required', 'string'],
            'vendor_category_id' => ['sometimes', 'exists:location_vendor_categories,id' ],
            'location_id' => ['sometimes', 'exists:locations,id'],
            'poc_name' => ['string','sometimes'],
            'poc_email' => ['string','sometimes'],
            'poc_phone' => ['string', 'min:10','sometimes'],
        ];
    }

    public function handle(LocationVendor $location_vendor, array $data): LocationVendor
    {
        LocationVendorAggregate::retrieve($location_vendor->id)->update($data)->persist();

        return $location_vendor->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locationVendors.update', LocationVendor::class);
    }

    public function asController(ActionRequest $request, LocationVendor $location_vendor): LocationVendor
    {
        $data = $request->validated();

        return $this->handle(
            $location_vendor,
            $data,
        );
    }

    public function htmlResponse(LocationVendor $location_vendor): RedirectResponse
    {
        Alert::success("Vendor '{$location_vendor->poc_name}' was updated")->flash();

        return Redirect::route('locationVendors.edit', $location_vendor->id);
    }
}
