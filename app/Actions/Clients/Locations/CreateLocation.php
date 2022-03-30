<?php

namespace App\Actions\Clients\Locations;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateLocation
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
            'poc_last' =>['sometimes'],
            'name' => ['required', 'max:50'],
            'city' => ['required', 'max:30'],
            'state' => ['required', 'size:2'],
            'client_id' => ['required'],
            'address1' => ['required','max:200'],
            'address2' => [],
            'zip' => ['required', 'size:5'],
            'phone' => [],
            'poc_first' => [],
            'poc_phone' => [],
            'open_date' => [],
            'close_date' => [],
            'location_no' => ['required', 'max:50'],
            'gymrevenue_id' => [],
        ];
    }

    public function handle($data, $current_user = null)
    {
        //        $id = Uuid::new();//we should use uuid here
        $gymrevenue_id = GenerateGymRevenueId::run($data['client_id']);
        $id = (Location::max('id') ?? 0) + 1;
        $data['id'] = $id;
        $data['gymrevenue_id'] = $gymrevenue_id;
        ClientAggregate::retrieve($data['client_id'])->createLocation($current_user->id ?? "Auto Generated", $data)->persist();

        return Location::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('locations.create', Location::class);
    }

    public function asController(ActionRequest $request)
    {

        $location = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Locataion '{$location->name}' was created")->flash();

        return Redirect::route('locations');
    }

}
