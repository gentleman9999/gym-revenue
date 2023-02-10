<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Enums\SecurityGroupEnum;
use Illuminate\Support\Collection;

final class SecurityGroups
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $_args
     *
     * @return Collection<array<string, mixed>>
     */
    public function __invoke($_, array $_args): Collection
    {
        // TODO implement the resolver
        return collect(SecurityGroupEnum::cases())->keyBy('name')->except('ADMIN')->values()->map(function ($s) {
            return ['value' => $s->value, 'name' => $s->name];
        });
    }
}
