<?php

namespace App\Domain\Departments\Actions;

use App\Domain\Departments\DepartmentAggregate;
use App\Models\Department;
use App\Models\Position;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteDepartment
{
    use AsAction;

    public function handle(string $id): Position
    {
        $department = Department::withTrashed()->findOrFail($id);
        DepartmentAggregate::retrieve($id)->delete()->persist();

        return $department;
    }
}
