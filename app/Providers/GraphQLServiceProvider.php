<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Locations\Enums\LocationType;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Register any custom types for the GraphQL schema.
     *
     */
    public function boot(TypeRegistry $typeRegistry): void
    {
        $typeRegistry->register(
            new LaravelEnumType(LocationType::class, 'LocationType')
        );
    }
}
