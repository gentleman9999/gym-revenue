<?php

namespace App\Domain\CalendarAttendees\Actions;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendeeAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class AddCalendarAttendee
{
    use AsAction;

    public function handle(array $data): CalendarAttendee
    {
        $id = Uuid::new();
        CalendarAttendeeAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return CalendarAttendee::findOrFail($id);
    }
}
