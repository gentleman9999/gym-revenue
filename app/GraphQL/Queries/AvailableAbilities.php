<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use Bouncer;
use Silber\Bouncer\Database\Ability;

final class AvailableAbilities
{
    /**
     * @param  null  $_
     * @param array<mixed>  $_args
     */
    public function __invoke($_, array $_args): Ability
    {
        // TODO implement the resolver
        return Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']);
    }
}
