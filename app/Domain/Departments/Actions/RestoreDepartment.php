<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\DepartmentAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreDepartment
{
    use AsAction;

    public function handle(string $id): Department
    {
        DepartmentAggregate::retrieve($id)->restore()->persist();

        return Department::withTrashed()->findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('departments.restore', Department::class);
    }

    public function asController(ActionRequest $request, Department $department): Department
    {
        return $this->handle(
            $department->id,
        );
    }

    public function htmlResponse(Department $department): RedirectResponse
    {
        Alert::success("Department '{$department->name}' was restored")->flash();

        return Redirect::back();
    }
}
