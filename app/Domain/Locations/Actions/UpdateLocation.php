<?php

declare(strict_types=1);

namespace App\Domain\Locations\Actions;

use App\Domain\Locations\LocationAggregate;
use App\Domain\Locations\Projections\Location;
use App\Enums\LocationTypeEnum;
use App\Enums\StatesEnum;
use App\Http\Middleware\InjectClientId;
use App\Rules\AddressCity;
use App\Rules\AddressLine;
use App\Rules\AddressState;
use App\Rules\AddressZip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;
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
            'name' => ['sometimes', 'max:50'],
            'city' => ['sometimes', 'max:30', new AddressCity()],
            'state' => ['sometimes', 'size:2', new Enum(StatesEnum::class), new AddressState()],
//            'client_id' => ['sometimes', 'exists:clients,id'],
            'address1' => ['sometimes','max:200', new AddressLine()],
            'address2' => [],
            'zip' => ['sometimes', 'required', 'size:5', new AddressZip()],
            'latitude' => ['sometimes', 'numeric', 'regex:/^(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))$/'],
            'longitude' => ['sometimes', 'numeric', 'regex:/^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$/'],
            'phone' => ['sometimes', ],
            'poc_first' => ['sometimes', ],
            'poc_phone' => ['sometimes', ],
            'opened_at' => ['sometimes', ],
            'location_no' => ['sometimes', 'max:50', 'exists:locations,location_no'],
            'gymrevenue_id' => ['sometimes', 'nullable', 'exists:locations,gymrevenue_id'],
            'default_team_id' => ['sometimes', 'nullable', 'exists:teams,id'],
            'location_type' => ['sometimes',  new Enum(LocationTypeEnum::class)],
            'presale_opened_at' => ['sometimes'],
            'presale_started_at' => ['sometimes'],
            'capacity' => ['sometimes','integer'],
        ];
    }

    /**
     * Validate the address provided using USPS API after main rules
     * Which also sends back correct address1, city and state
     *
     * @return void
     */
    public function afterValidator(Validator $validator, ActionRequest $request): void
    {
        /**
         * @TODO: Send the suggestion data back to UI, and display to the User.
         * They can make a choice (confirm/cancel), and have it update if confirmed
         */
        session()->forget('address_validation');
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

    public function getValidationAttributes(): array
    {
        return [
            'addres1' => 'address line 1',
        ];
    }
}
