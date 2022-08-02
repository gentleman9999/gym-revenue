<?php

namespace App\Aggregates\Clients;

use App\Aggregates\Clients\Traits\ClientActions;
use App\Aggregates\Clients\Traits\ClientApplies;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientAggregate extends AggregateRoot
{
    use ClientApplies;
    use ClientActions;

    protected array $employee_activity = [];

    protected static bool $allowConcurrency = true;
}
