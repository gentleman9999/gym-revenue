<?php

namespace App\Actions\Clients\Locations;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
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
    public function rules()
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
        ];
    }

    public function handle($data, $current_user = null)
    {
        ClientAggregate::retrieve($data['client_id'])->updateLocation($current_user->id ?? "Auto Generated", $data)->persist();

        return Location::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('locations.update', Location::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['client_id'] = $request->user()->currentClientId();

        $location = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Location '{$location->name}' was updated")->flash();

        return Redirect::route('locations.edit', $location->id);
//        return Redirect::back();
    }
}
