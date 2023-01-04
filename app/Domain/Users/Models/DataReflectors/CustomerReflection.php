<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflectors;

use App\Domain\Users\Models\Customer;

class CustomerReflection extends Customer
{
    /**
     * Define the table name
     *
     * @var string
     */
    protected $table = 'customers';

    protected $guarded = [];
}
