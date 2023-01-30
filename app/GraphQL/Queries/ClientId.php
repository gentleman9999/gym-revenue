<?php

namespace App\GraphQL\Queries;

use App\Domain\Users\Models\User;

final class ClientId
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        return User::find($args["id"])->client_id;
        // TODO implement the resolver
    }
}
