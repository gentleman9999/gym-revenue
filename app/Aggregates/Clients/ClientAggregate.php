<?php

namespace App\Aggregates\Clients;

use App\Aggregates\Clients\Traits\ClientActions;
use App\Aggregates\Clients\Traits\ClientApplies;
use App\Aggregates\Clients\Traits\ClientGetters;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientAggregate extends AggregateRoot
{
    use ClientGetters, ClientApplies, ClientActions;

    protected string $default_team = '';
    protected array $teams = [];

    protected static bool $allowConcurrency = true;

}
