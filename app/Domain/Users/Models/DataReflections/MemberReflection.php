<?php

declare(strict_types=1);

namespace App\Domain\Users\Models\DataReflections;

use App\Domain\Users\Models\Member;

class MemberReflection extends Member
{
    /** @var string  */
    protected $table = 'members';

    /** @var array<string>  */
    protected $guarded = [];
}
