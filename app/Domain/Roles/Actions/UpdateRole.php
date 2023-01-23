<?php

namespace App\Domain\Roles\Actions;

use App\Actions\GymRevAction;
use App\Domain\Roles\Role;
use App\Domain\Roles\RoleAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateRole extends GymRevAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string','min:2'],
            'ability_names' => ['sometimes', 'array'],
            'group' => ['sometimes', 'integer', 'min:1', 'max:6'],
        ];
    }

    public function handle(Role $role, array $data): Role
    {
        RoleAggregate::retrieve($role->id)->update($data)->persist();

        return $role->refresh();
    }

    public function mapArgsToHandle($args): array
    {
        $role = $args['input'];

        return [Role::find($role['id']), $role];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.update', Role::class);
    }

    public function asController(ActionRequest $request, Role $role): Role
    {
        return $this->handle(
            $role,
            $request->validated(),
        );
    }

    public function htmlResponse(Role $role): RedirectResponse
    {
        Alert::success("Role '{$role->name}' was updated")->flash();

        return Redirect::route('roles.edit', $role->id);
    }
}
