<?php

namespace App\Domain\Users\Actions;

use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

class BaseEndUserAction
{
    use AsAction;

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }
}
