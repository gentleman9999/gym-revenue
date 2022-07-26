<?php

namespace App\Domain\EndUsers\Reactors;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

abstract class BaseEndUserReactor extends Reactor implements ShouldQueue
{
    abstract public static function getModel(): EndUser;

    abstract public static function getAggregate(): EndUserAggregate;
}
