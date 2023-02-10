<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Domain\Users\Models\User;

final class IsClientUser
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args): bool
    {
        // TODO implement the resolver
        return User::find($args["id"])->isClientUser();
    }
}
