<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class BaseEndUserAction
{
    use AsAction;

    abstract protected static function getModel(): EndUser;

    abstract protected static function getAggregate(): EndUserAggregate;

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }
}
