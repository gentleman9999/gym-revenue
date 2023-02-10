<?php

declare(strict_types=1);

namespace App\Actions;

use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class GymRevAction
{
    use AsAction;

    /**
     * When an action is called as a graphql resolver, arguments may need to be manually
     * mapped to match the handle function's signature
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    public function mapArgsToHandle(array $args): array
    {
        return $args;
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $this->mapArgsToHandle($args);

        return $this->handle(...$this->mapArgsToHandle($args));
//        return $this->handle($args);
    }
}
