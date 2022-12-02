<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class BaseEndUserAction
{
    use AsAction;

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    /**
     * GraphQL Invoke
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): EndUser
    {
        return $this->handle($args);
    }
}
