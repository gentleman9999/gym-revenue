<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Actions;

use App\Domain\LocationVendorCategories\LocationVendorCategoryAggregate;
use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreLocationVendorCategory
{
    use AsAction;

    public function handle(LocationVendorCategory $location_vendor_category): LocationVendorCategory
    {
        LocationVendorCategoryAggregate::retrieve($location_vendor_category->id)->restore()->persist();

        return $location_vendor_category->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locationVendorCategorys.restore', LocationVendorCategory::class);
    }

    public function asController(Request $request, LocationVendorCategory $location_vendor_category): LocationVendorCategory
    {
        return $this->handle(
            $location_vendor_category,
        );
    }

    public function htmlResponse(LocationVendorCategory $location_vendor_category): RedirectResponse
    {
        Alert::success("LocationVendorCategory '{$location_vendor_category->name}' was restored")->flash();

        return Redirect::back();
    }
}
