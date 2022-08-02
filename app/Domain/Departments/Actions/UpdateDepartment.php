<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\Department;
use App\Domain\Departments\DepartmentAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateDepartment
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
            'positions' => ['array', 'sometimes'],
        ];
    }

    public function handle(Department $department, array $payload): Department
    {
        DepartmentAggregate::retrieve($department->id)->update($payload)->persist();

        return $department->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('departments.create', Department::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, Department $department)
    {
        return $this->handle(
            $department,
            $request->validated(),
        );
    }

    public function htmlResponse(Department $department): RedirectResponse
    {
        Alert::success("Department '{$department->name}' was updated")->flash();

        return Redirect::route('departments.edit', $department->id);
    }
}
