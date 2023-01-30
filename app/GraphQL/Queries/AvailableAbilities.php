<?php

namespace App\GraphQL\Queries;

use Bouncer;

final class AvailableAbilities
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        // TODO implement the resolver
        return Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']);
    }
}
