<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflections;

use App\Domain\Users\Models\Customer;

class CustomerReflection extends Customer
{
    /** @var string  */
    protected $table = 'customers';

    /** @var array<string>  */
    protected $guarded = [];
}
