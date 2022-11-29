<?php

namespace App\Domain\Locations\Actions;

use App\Domain\Locations\LocationAggregate;
use App\Domain\Locations\Projections\Location;
use App\Enums\LocationTypeEnum;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLocation
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
            'poc_last' => ['sometimes'],
            'name' => ['sometimes', 'required', 'max:50'],
            'city' => ['sometimes', 'required', 'max:30'],
            'state' => ['sometimes', 'required', 'size:2'],
//            'client_id' => ['sometimes', 'required', 'exists:clients,id'],
            'address1' => ['sometimes', 'required','max:200'],
            'address2' => [],
            'zip' => ['sometimes', 'required', 'size:5'],
            'phone' => ['sometimes', ],
            'poc_first' => ['sometimes', ],
            'poc_phone' => ['sometimes', ],
            'open_date' => ['sometimes', ],
            'close_date' => ['sometimes', ],
            'location_no' => ['sometimes', 'required', 'max:50', 'exists:locations,location_no'],
            'gymrevenue_id' => ['sometimes', 'nullable', 'exists:locations,gymrevenue_id'],
            'default_team_id' => ['sometimes', 'nullable', 'exists:teams,id'],
            'location_type' => ['required',  new Enum(LocationTypeEnum::class)],
        ];
    }

    public function handle(Location $location, array $data): Location
    {
        LocationAggregate::retrieve($location->id)->update($data)->persist();

        return $location->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.update', Location::class);
    }

    public function asController(ActionRequest $request, Location $location): Location
    {
        $data = $request->validated();

        return $this->handle(
            $location,
            $data,
        );
    }

    public function htmlResponse(Location $location): RedirectResponse
    {
        Alert::success("Location '{$location->name}' was updated")->flash();

        return Redirect::route('locations.edit', $location->id);
    }
}
