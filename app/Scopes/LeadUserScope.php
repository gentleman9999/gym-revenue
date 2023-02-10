<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Enums\UserTypesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LeadUserScope implements Scope
{
    public function apply(Builder $builder, Model $_): void
    {
        $builder->whereUserType(UserTypesEnum::LEAD);
    }
}
