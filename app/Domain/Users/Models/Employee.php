<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Scopes\ClientScope;
use App\Scopes\EmployeeUserScope;

class Employee extends User
{
    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new ClientScope());
        static::addGlobalScope(new EmployeeUserScope());
    }
}
