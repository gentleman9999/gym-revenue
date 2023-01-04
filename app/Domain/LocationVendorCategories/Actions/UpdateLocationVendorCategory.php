<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Actions;

use App\Domain\LocationVendorCategories\LocationVendorCategoryAggregate;
use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLocationVendorCategory
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
            'client_id' => ['sometimes', 'exists:clients,id'],
        ];
    }

    public function handle(LocationVendorCategory $location_vendor_category, array $data): LocationVendorCategory
    {
        LocationVendorCategoryAggregate::retrieve($location_vendor_category->id)->update($data)->persist();

        return $location_vendor_category->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locationVendorCategorys.update', LocationVendorCategory::class);
    }

    public function asController(ActionRequest $request, LocationVendorCategory $location_vendor_category): LocationVendorCategory
    {
        $data = $request->validated();

        return $this->handle(
            $location_vendor_category,
            $data,
        );
    }

    public function htmlResponse(LocationVendorCategory $location_vendor_category): RedirectResponse
    {
        Alert::success("LocationVendorCategory '{$location_vendor_category->name}' was updated")->flash();

        return Redirect::route('locationVendorCategorys.edit', $location_vendor_category->id);
    }
}
