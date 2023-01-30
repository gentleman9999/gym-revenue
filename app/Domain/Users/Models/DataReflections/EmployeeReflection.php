<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflections;

use App\Domain\Users\Models\Employee;

class EmployeeReflection extends Employee
{
    /**
     * Define the table name
     *
     * @var string
     */
    protected $table = 'employees';

    protected $guarded = [];
}
