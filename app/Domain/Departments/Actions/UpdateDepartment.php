<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\DepartmentAggregate;
use App\Models\Department;
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
        ];
    }

    public function handle(string $id, array $payload): Department
    {
        DepartmentAggregate::retrieve($id)->update($payload)->persist();

        return Department::findOrFail($id);
    }

    public function asController(ActionRequest $request, Department $department)
    {
        $department = $this->handle(
            $department->id,
            $request->validated(),
        );

        Alert::success("Position '{$department->name}' was updated")->flash();

        return Redirect::route('positions.edit', $department->id);
    }
}
