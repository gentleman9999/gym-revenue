<?php

namespace App\Domain\CalendarAttendees\Actions;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendeeAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class RemoveCalendarAttendee
{
    use AsAction;

    public function handle(CalendarAttendee $calendarAttendee): CalendarAttendee
    {
        CalendarAttendeeAggregate::retrieve($calendarAttendee->id)
            ->delete()
            ->persist();

        return $calendarAttendee;
    }
}
