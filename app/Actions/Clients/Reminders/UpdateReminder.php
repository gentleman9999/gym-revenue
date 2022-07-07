<?php

namespace App\Actions\Clients\Reminders;

use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Silber\Bouncer\Database\Role;

class UpdateReminder
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
            'name' => ['sometimes', 'string', 'required'],
            'id' => ['sometimes', 'integer', 'required'],
            'description' => ['sometimes', 'string'],
            'remind_time' => ['sometimes', 'string'],
            'triggered_at' => ['sometimes', 'timestamp'],
            'ability_names' => ['sometimes', 'array', 'sometimes'],
            'group' => ['sometimes', 'required', 'integer','min:1', 'max:6'],
        ];
    }

    public function handle($data, $current_user)
    {
        $client_id = $current_user->currentClientId();
        $data['client_id'] = $client_id;
        ClientAggregate::retrieve($client_id)->updateRole($current_user->id, $data)->persist();

        return Role::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.update', Role::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $role = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Role '{$role->name}' was updated")->flash();

//        return Redirect::route('roles');
        return Redirect::route('roles.edit', $id);
    }
}
