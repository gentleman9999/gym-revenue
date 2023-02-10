<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors\Actions;

use App\Domain\LocationVendors\LocationVendorAggregate;
use App\Domain\LocationVendors\Projections\LocationVendor;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateLocationVendor
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
            'name' => ['required', 'max:50'],
            'client_id' => ['required', 'exists:clients,id'],
            'vendor_category_id' => ['required', 'exists:location_vendor_categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'poc_name' => ['string','required'],
            'poc_email' => ['string','required_without_all:poc_phone'],
            'poc_phone' => ['string', 'min:10','required_without_all:poc_email'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): LocationVendor
    {
        $id = (string) Uuid::new();//we should use uuid here
        LocationVendorAggregate::retrieve($id)->create($data)->persist();

        return LocationVendor::findOrFail($id);
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

        return $current_user->can('locationVendors.create', LocationVendor::class);
    }

    public function asController(ActionRequest $request): LocationVendor
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(LocationVendor $location_vendor): RedirectResponse
    {
        Alert::success("Vendor '{$location_vendor->poc_name}' was created")->flash();

        return Redirect::back();
    }
}
