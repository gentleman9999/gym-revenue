<?php

namespace App\Actions\Clients\Roles;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Location;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashRole
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

    public function handle($current_user, $id)
    {
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->trashRole($current_user->id, $id)->persist();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.trash', Role::class);
    }

    public function asController(Request $request, $id)
    {
        $role = Location::findOrFail($id);
        $this->handle(
            $request->user(),
            $id
        );

        Alert::success("Role '{$role->name}' was trashed")->flash();

        return Redirect::route('roles');
    }
}
