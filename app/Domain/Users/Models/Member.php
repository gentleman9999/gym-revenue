<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Scopes\ClientScope;

class Member extends EndUser
{
    protected $table = 'members';

    protected $appends = ['details_desc'];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new ClientScope());
    }
}
