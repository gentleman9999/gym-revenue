<?php

declare(strict_types=1);

namespace App\Domain\Users\Reactors;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

abstract class BaseEndUserReactor extends Reactor implements ShouldQueue
{
    abstract public static function getModel(): EndUser;

    abstract public static function getAggregate(): UserAggregate;
}
