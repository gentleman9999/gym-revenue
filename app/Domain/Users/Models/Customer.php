<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Scopes\CustomerUserScope;

class Customer extends EndUser
{
    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new CustomerUserScope());
    }
}
