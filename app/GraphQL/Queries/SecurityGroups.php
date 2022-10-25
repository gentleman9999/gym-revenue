<?php

namespace App\GraphQL\Queries;

use App\Enums\SecurityGroupEnum;

final class SecurityGroups
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        return collect(SecurityGroupEnum::cases())->keyBy('name')->except('ADMIN')->values()->map(function ($s) {
            return ['value' => $s->value, 'name' => $s->name];
        });
    }
}
