<?php

declare(strict_types=1);

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ObfuscatedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     */
    public function apply(Builder $builder, Model $_): void
    {
        $builder->where('obfuscated_at', null);
    }
}
