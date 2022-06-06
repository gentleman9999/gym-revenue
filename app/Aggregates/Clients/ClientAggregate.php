<?php

namespace App\Aggregates\Clients;

use App\Aggregates\Clients\Traits\ClientActions;
use App\Aggregates\Clients\Traits\ClientApplies;
use App\Aggregates\Clients\Traits\ClientGetters;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientAggregate extends AggregateRoot
{
    use ClientGetters;
    use ClientApplies;
    use ClientActions;

    protected string $home_team = '';
    protected string $team_prefix = '';
    protected array $teams = [];
    protected array $users = [];
    protected array $employee_activity = [];

    protected static bool $allowConcurrency = true;
}
