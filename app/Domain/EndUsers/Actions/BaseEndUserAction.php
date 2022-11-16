<?php

namespace App\Domain\EndUsers\Actions;

use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

abstract class BaseEndUserAction
{
    use AsAction;

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }
}
