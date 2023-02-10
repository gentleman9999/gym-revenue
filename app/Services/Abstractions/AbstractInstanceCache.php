<?php

declare(strict_types=1);

namespace App\Services\Abstractions;

use App\Domain\Clients\Projections\Client;

abstract class AbstractInstanceCache
{
    private static array $instances = [];

    abstract protected function __construct(Client $client);

    public static function get(Client $client): static
    {
        return static::$instances[$client->id] ?? static::$instances[$client->id] = new static($client);
    }
}
