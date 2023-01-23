<?php

namespace App\Domain\Roles\Actions;

use App\Actions\GymRevAction;
use App\Domain\Roles\Role;
use App\Domain\Roles\RoleAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateRole extends GymRevAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string','min:2'],
            'ability_names' => ['array', 'sometimes'],
            'group' => ['required', 'integer','min:1', 'max:6'],
        ];
    }

    public function handle(array $data): Role
    {
        $id = (Role::withoutGlobalScopes()->max('id') ?? 0) + 1;

        RoleAggregate::retrieve($id)->create($data)->persist();

        return Role::findOrFail($id);
    }

    public function mapArgsToHandle($args): array
    {
        return [$args['input']];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.create', Role::class);
    }

    public function asController(ActionRequest $request): Role
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(Role $role): RedirectResponse
    {
        Alert::success("Role '{$role->name}' was created")->flash();

        return Redirect::route('roles.edit', $role->id);
    }
}
