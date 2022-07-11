<?php

namespace App\Actions\Clients\Roles;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateRole
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
            'name' => ['string', 'required'],
            'id' => ['integer', 'sometimes', 'nullable'],
            'ability_names' => ['array', 'sometimes'],
            'group' => ['required', 'integer','min:1', 'max:6'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        $id = (Role::max('id') ?? 0) + 1;
        $data['id'] = $id;
        $client_id = $current_user->currentClientId();
        $data['client_id'] = $client_id;

        ClientAggregate::retrieve($client_id)->createRole($current_user->id ?? "Auto Generated", $data)->persist();

        return Role::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.create', Role::class);
    }

    public function asController(ActionRequest $request)
    {
        $role = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Role '{$role->name}' was created")->flash();

        return Redirect::route('roles');
    }
}
