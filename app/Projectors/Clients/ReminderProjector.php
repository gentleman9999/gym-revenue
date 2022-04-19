<?php

namespace App\Projectors\Clients;


use App\Models\Reminder;
use App\StorableEvents\Clients\Reminder\ReminderCreated;
use App\StorableEvents\Clients\Reminder\ReminderDeleted;
use App\StorableEvents\Clients\Reminder\ReminderTriggered;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ReminderProjector extends Projector
{
    public function onReminderCreated(ReminderCreated $event)
    {
        Reminder::create($event->payload);
    }

    public function onReminderDeleted(ReminderDeleted $event)
    {
        Reminder::findOrFail($event->id)->delete();
    }

    public function onReminderTriggered(ReminderTriggered $event)
    {
        Reminder::findOrFail($event->id)->update(['triggered_at' => $event->created_at]);
    }

}
