<?php

namespace App\GraphQL\Queries;

use App\Domain\Users\Models\User;

final class IsClientUser
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        return User::find($args["id"])->isClientUser();
    }
}
