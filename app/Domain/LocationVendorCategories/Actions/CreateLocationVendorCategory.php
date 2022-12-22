<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories\Actions;

use App\Domain\LocationVendorCategories\LocationVendorCategoryAggregate;
use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateLocationVendorCategory
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
        ];
    }

    public function handle(array $data): LocationVendorCategory
    {
        $id = (string)Uuid::new();//we should use uuid here
        LocationVendorCategoryAggregate::retrieve($id)->create($data)->persist();

        return LocationVendorCategory::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locationVendorCategoryies.create', LocationVendorCategory::class);
    }

    public function asController(ActionRequest $request): LocationVendorCategory
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(LocationVendorCategory $location_vendor_category): RedirectResponse
    {
        Alert::success("Locataion '{$location_vendor_category->name}' was created")->flash();

        return Redirect::route('locationVendorCategoryies.edit', $location_vendor_category->id);
    }
}
