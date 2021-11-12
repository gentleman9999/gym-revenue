<?php

namespace App\Reactors\Endusers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class EndUserActivityReactor extends Reactor implements ShouldQueue
{
    public function onEventHappened(EventHappened $event)
    {
    }
}
