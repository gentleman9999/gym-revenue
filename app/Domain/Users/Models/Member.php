<?php

declare(strict_types=1);

namespace App\Domain\Users\Models;

use App\Scopes\MemberUserScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends EndUser
{
    protected $appends = ['details_desc'];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope(new MemberUserScope());
    }

    public function getDetailsDescAttribute(): HasMany
    {
        return $this->detailsDesc();
    }
}
