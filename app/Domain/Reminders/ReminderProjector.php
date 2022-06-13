<?php

namespace App\Domain\Reminders;

use App\Domain\Reminders\Events\ReminderCreated;
use App\Domain\Reminders\Events\ReminderDeleted;
use App\Domain\Reminders\Events\ReminderTriggered;
use App\Domain\Reminders\Events\ReminderUpdated;
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
        Reminder::findOrFail($event->id)->update(['triggered_at' => $event->createdAt()]);
    }
}
