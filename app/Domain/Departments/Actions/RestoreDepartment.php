<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\DepartmentAggregate;
use App\Models\Department;
use App\Models\Position;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreDepartment
{
    use AsAction;

    public function handle(string $id): Position
    {
        DepartmentAggregate::retrieve($id)->restore()->persist();

        return Department::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }
}
