<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\Department;
use App\Domain\Departments\DepartmentAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateDepartment
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
            'client_id' => ['string', 'required'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        $id = Uuid::new();

        $aggy = DepartmentAggregate::retrieve($id);

        $aggy->create($data)->persist();

        return Department::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('departments.create', Department::class);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
            $request->user(),
        );
    }

    public function htmlResponse(Department $department): RedirectResponse
    {
        Alert::success("Department '{$department->name}' was created")->flash();

        return Redirect::route('departments.edit', $department->id);
    }
}
