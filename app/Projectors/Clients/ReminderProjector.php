<?php

namespace App\Projectors\Clients;

use App\Models\Reminder;
use App\StorableEvents\Users\Reminder\ReminderCreated;
use App\StorableEvents\Users\Reminder\ReminderDeleted;
use App\StorableEvents\Users\Reminder\ReminderTriggered;
use App\StorableEvents\Users\Reminder\ReminderUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ReminderProjector extends Projector
{
    public function onReminderCreated(ReminderCreated $event)
    {
        Reminder::create($event->payload);
    }

    public function onReminderUpdated(ReminderUpdated $event)
    {
        Reminder::findOrFail($event->payload['id'])->updateOrFail($event->payload);
    }

    public function onReminderDeleted(ReminderDeleted $event)
    {
        Reminder::findOrFail($event->id)->delete();
    }

    public function onReminderTriggered(ReminderTriggered $event)
    {
        Reminder::findOrFail($event->id)->update(['triggered_at' => $event->metaData()['created-at']]);
    }
}
