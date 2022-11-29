<?php

namespace App\Domain\Locations\Actions;

use App\Domain\Locations\LocationAggregate;
use App\Domain\Locations\Projections\Location;
use App\Enums\LocationTypeEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateLocation
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
            'name' => ['required', 'max:50'],
            'city' => ['required', 'max:30'],
            'state' => ['required', 'size:2'],
            'client_id' => ['required', 'exists:clients,id'],
            'address1' => ['required','max:200'],
            'address2' => [],
            'zip' => ['required', 'size:5'],
            'phone' => [],
            'poc_first' => [],
            'poc_phone' => [],
            'open_date' => [],
            'close_date' => [],
            'location_no' => ['required', 'max:50'],
            'gymrevenue_id' => ['sometimes', 'nullable', 'exists:locations,gymrevenue_id'],
            'default_team_id' => ['sometimes', 'nullable', 'exists:teams,id'],
            'shouldCreateTeam' => ['sometimes', 'boolean'],
            'location_type' => ['required',  new Enum(LocationTypeEnum::class)],
        ];
    }

    public function handle(array $data): Location
    {
        $id = Uuid::new();//we should use uuid here
        $gymrevenue_id = GenerateGymRevenueId::run($data['client_id']);
        $data['gymrevenue_id'] = $gymrevenue_id;
        LocationAggregate::retrieve($id)->create($data)->persist();

        return Location::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.create', Location::class);
    }

    public function asController(ActionRequest $request): Location
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(Location $location): RedirectResponse
    {
        Alert::success("Locataion '{$location->name}' was created")->flash();

        return Redirect::route('locations.edit', $location->id);
    }
}
