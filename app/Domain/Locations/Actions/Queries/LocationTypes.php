<?php

declare(strict_types=1);

namespace App\Domain\Locations\Actions\Queries;

use App\Domain\Locations\Enums\LocationType;
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
        return LocationType::asOptionsArray();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }
}
