<?php

namespace App\Actions\Clients\Roles;

use App\Models\Clients\Location;
use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Silber\Bouncer\Database\Role;

class DeleteRole
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
        $role = Role::findOrFail($id);
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->deleteRole($current_user->id, $id)->persist();
        return $role;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('roles.delete', Role::class);
    }

    public function asController(Request $request, $id)
    {
        $role = $this->handle(
            $request->user(),
            $id
        );

        Alert::success("Role '{$role->name}' was deleted")->flash();

        return Redirect::route('roles');

    }
}
