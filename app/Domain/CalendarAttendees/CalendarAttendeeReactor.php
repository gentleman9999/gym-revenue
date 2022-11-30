<?php

namespace App\Domain\CalendarAttendees;

use App\Domain\CalendarAttendees\Actions\InviteAttendee;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeAccepted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeAdded;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeclined;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeDeleted;
use App\Domain\CalendarAttendees\Events\CalendarAttendeeInvited;
use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\Reminders\Actions\CreateReminder;
use App\Domain\Reminders\Actions\DeleteReminder;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\User;
use App\Models\Utility\AppState;
use function env;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mailgun\Mailgun;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class CalendarAttendeeReactor extends Reactor implements ShouldQueue
{
    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event): void
    {
        if (array_key_exists('is_task', $event->payload)) {
            if (! $event->payload['is_task']) {
                if (! AppState::isSimuationMode()) {
                    InviteAttendee::run($event);
                }
            }
        } else {
            if (! AppState::isSimuationMode()) {
                InviteAttendee::run($event);
            }
        }
    }

    public function onCalendarAttendeeInvited(CalendarAttendeeInvited $event): void
    {
        $mg = Mailgun::create(env('MAILGUN_SECRET'));
        $mg->messages()->send(env('MAILGUN_DOMAIN'), [
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => $event->payload['endUser']['email'],
            'subject' => $event->payload['subject'],
            'html' => $event->payload['body'],
        ]);
    }

    public function onCalendarAttendeeAccepted(CalendarAttendeeAccepted $event): void
    {
        $calendarAttendee = CalendarAttendee::with('event')->findOrFail($event->aggregateRootUuid());
        if ($calendarAttendee->entity_type === User::class) {
            CreateReminder::run([
                'entity_type' => CalendarEvent::class,
                'entity_id' => $calendarAttendee->event->id,
                'user_id' => $calendarAttendee->user_id,
                'name' => 'Default Reminder',
                'remind_time' => 30,
            ]);
        }
    }

    public function onCalendarAttendeeDeclined(CalendarAttendeeDeclined $event): void
    {
        $calendarAttendee = CalendarAttendee::with('event')->findOrFail($event->aggregateRootUuid());
        if ($calendarAttendee->entity_type === User::class) {
            $reminders = Reminder::whereUserId($calendarAttendee->user_id)->whereEntityType(CalendarEvent::class)->whereEntityId($calendarAttendee->event->id)->get();
            $reminders->each(fn ($reminder) => DeleteReminder::run($reminder));
        }
    }

    public function onCalendarAttendeeDeleted(CalendarAttendeeDeleted $event): void
    {
        $calendarAttendee = CalendarAttendee::withTrashed()->with('event')->findOrFail($event->aggregateRootUuid());
        if ($calendarAttendee->entity_type === User::class) {
            $reminders = Reminder::whereUserId($calendarAttendee->user_id)->whereEntityType(CalendarEvent::class)->whereEntityId($calendarAttendee->event->id)->get();
            $reminders->each(fn ($reminder) => DeleteReminder::run($reminder));
        }
        $calendarAttendee->writeable()->forceDelete();
    }
}
