<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflections;

use App\Domain\Users\Models\Lead;

class LeadReflection extends Lead
{
    /**
     * Define the table name
     *
     * @var string
     */
    protected $table = 'leads';

    protected $guarded = [];
}
