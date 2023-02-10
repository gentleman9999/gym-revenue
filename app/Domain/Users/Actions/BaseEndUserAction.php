<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\EndUser;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

class BaseEndUserAction
{
    use AsAction;

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    /**
     * GraphQL Invoke
     * @param  null  $_
     * @param array<mixed>  $args
     */
    public function __invoke($_, array $args): EndUser
    {
        return $this->handle($args);
    }
}
