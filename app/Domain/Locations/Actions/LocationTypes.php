<?php

namespace App\Domain\Locations\Actions;

use App\Enums\LocationTypeEnum;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

class LocationTypes
{
    use AsAction;

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        return $this->handle();
    }

    public function handle(): array
    {
        return LocationTypeEnum::asArray();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }
}
