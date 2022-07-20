<?php

namespace App\Domain\Roles\Actions;

use App\Domain\Roles\Role;
use App\Domain\Roles\RoleAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateRole
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
            'ability_names' => ['sometimes', 'array', 'sometimes'],
            'group' => ['sometimes', 'required', 'integer','min:1', 'max:6'],
        ];
    }

    public function handle(Role $role, array $data)
    {
        RoleAggregate::retrieve($role->id)->update($data)->persist();

        return $role->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.update', Role::class);
    }

    public function asController(ActionRequest $request, Role $role)
    {
        $data = $request->validated();

        return $this->handle(
            $role,
            $data,
        );
    }

    public function htmlResponse(Role $role): RedirectResponse
    {
        Alert::success("Role '{$role->name}' was updated")->flash();

        return Redirect::route('roles.edit', $role->id);
    }
}