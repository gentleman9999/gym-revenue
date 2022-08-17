<?php

namespace App\Domain\Reminders;

use App\Domain\Reminders\Events\ReminderCreated;
use App\Domain\Reminders\Events\ReminderDeleted;
use App\Domain\Reminders\Events\ReminderTriggered;
use App\Domain\Reminders\Events\ReminderUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class ReminderProjector extends Projector
{
    public function onReminderCreated(ReminderCreated $event): void
    {
        $reminder = (new Reminder())->writeable();
        $reminder->id = $event->payload['id'];
        $reminder->fill($event->payload);
        $reminder->save();
    }

    public function onReminderUpdated(ReminderUpdated $event): void
    {
        Reminder::find($event->payload['id'])->writeable()->updateOrFail($event->payload);
    }

    public function onReminderDeleted(ReminderDeleted $event): void
    {
        Reminder::findOrFail($event->id)->writeable()->delete();
    }

    public function onReminderTriggered(ReminderTriggered $event): void
    {
        Reminder::findOrFail($event->id)->writeable()->updateOrFail(['triggered_at' => $event->createdAt()]);
    }
}
