<?php

namespace App\Reactors\Clients;

use App\Actions\Users\Notifications\CreateNotification;
use App\Models\Reminder;
use App\Models\User;
use App\StorableEvents\Users\Reminder\ReminderTriggered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ReminderReactor extends Reactor implements ShouldQueue
{
    public function onReminderTriggered(ReminderTriggered $event)
    {
//        dd($event);
//        Logger::debug($event);
        $reminder = Reminder::findOrFail($event->id);
        $entity = null;
        if ($reminder->entity_type) {
            $entity = $reminder->entity_type::findOrFail($reminder->entity_id);
        }
        CreateNotification::run([
            'user_id' => $reminder->user_id,
            'state' => 'warning',
            'text' => "Reminder",
            'entity_type' => $reminder->entity_type,
            'entity' => $entity ,
            'type' => 'CALENDAR_EVENT_REMINDER',
            //TODO:we prob dont need to store text, since the UI should be build
            // the the view based on entity_type and the entity's data
            'misc' => [
                'remind_time' => $reminder->remind_time,
            ],
        ], User::find($reminder->user_id));
    }
}
