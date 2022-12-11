<?php

namespace App\Domain\Roles\Actions;

use App\Domain\Roles\Role;
use App\Domain\Roles\RoleAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteRole
{
    use AsAction;

    public function handle(Role $role): Role
    {
        RoleAggregate::retrieve($role->id)->delete()->persist();

        return $role;
    }

    public function __invoke($_, array $args): Role
    {
        $role = Role::find($args['id']);

        return $this->handle($role);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('roles.delete', Role::class);
    }

    public function asController(ActionRequest $request, Role $role): Role
    {
        return $this->handle(
            $role
        );
    }

    public function htmlResponse(Role $role): RedirectResponse
    {
        Alert::success("Role '{$role->name}' was deleted")->flash();

        return Redirect::route('roles');
    }
}
