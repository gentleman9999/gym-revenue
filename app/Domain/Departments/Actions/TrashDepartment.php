<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\DepartmentAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashDepartment
{
    use AsAction;

    public function handle(string $id): Department
    {
        DepartmentAggregate::retrieve($id)->trash()->persist();

        return Department::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('departments.trash', Position::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, Department $department): Department
    {
        return $this->handle(
            $department->id,
        );
    }

    public function htmlResponse(Department $department): RedirectResponse
    {
        Alert::success("Department '{$department->name}' was trashed")->flash();

        return Redirect::back();
    }
}
