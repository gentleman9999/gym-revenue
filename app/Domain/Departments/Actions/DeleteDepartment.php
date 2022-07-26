<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\Department;
use App\Domain\Departments\DepartmentAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteDepartment
{
    use AsAction;

    public function handle(Department $department): Position
    {
        DepartmentAggregate::retrieve($department->id)->delete()->persist();

        return $department;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('departments.delete', Position::class);
    }

    public function asController(ActionRequest $request, Department $department): Department
    {
        return $this->handle(
            $department,
        );
    }

    public function htmlResponse(Department $department): RedirectResponse
    {
        Alert::success("Department '{$department->name}' was deleted")->flash();

        return Redirect::back();
    }
}
