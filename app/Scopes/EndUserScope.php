<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Enums\UserTypesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class EndUserScope implements Scope
{
    public function apply(Builder $builder, Model $_): void
    {
        $builder->where('user_type', '!=', UserTypesEnum::EMPLOYEE);
    }
}
