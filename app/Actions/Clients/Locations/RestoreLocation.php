<?php

namespace App\Actions\Clients\Locations;

use App\Models\Clients\Location;
use Bouncer;
use App\Actions\Fortify\PasswordValidationRules;
use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreLocation
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($data, $current_user)
    {
        ClientAggregate::retrieve($data['id'])->restoreLocation($current_user->id ?? "Auto Generated", $data)->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('locations.restore', Location::class);
    }

    public function asController(Request $request, $id)
    {
        $location = Location::withTrashed()->findOrFail($id);

        $this->handle(
            $location->toArray(),
            $request->user(),
        );

        Alert::success("Location '{$location->name}' restored.")->flash();

//        return Redirect::route('users');
        return Redirect::back();
    }
}
