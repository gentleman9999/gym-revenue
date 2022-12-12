<?php

declare(strict_types=1);

namespace App\Domain\Locations\Actions;

use App\Domain\Locations\Enums\LocationType;
use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\Concerns\AsAction;

//TODO: This should be named GetLocationTypes, but then the graphql field will be "getLocationTypes" instead of "locationTypes".
//TODO: Look into custom field resolvers, or maybe just create a new directory in Actions called "Queries" and put this in there.
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
