<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflectors;

use App\Domain\Users\Models\Member;

class MemberReflection extends Member
{
    /**
     * Define the table name
     *
     * @var string
     */
    protected $table = 'members';

    protected $guarded = [];
}
