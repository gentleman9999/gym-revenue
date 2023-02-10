<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflections;

use App\Domain\Users\Models\Lead;

class LeadReflection extends Lead
{
   /** @var string  */
    protected $table = 'leads';

    /** @var array<string>  */
    protected $guarded = [];
}
