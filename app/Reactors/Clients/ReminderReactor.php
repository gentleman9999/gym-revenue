<?php

namespace App\Reactors\Clients;


use App\StorableEvents\Clients\Reminder\ReminderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ReminderReactor extends Reactor implements ShouldQueue
{
    public function onReminderCreated(ReminderCreated $event){


    }
}
