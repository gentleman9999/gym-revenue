<?php

declare(strict_types=1);

namespace App\Domain\CalendarAttendees\Actions;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendeeAggregate;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class AddCalendarAttendee
{
    use AsAction;

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): CalendarAttendee
    {
        $id = Uuid::get();
        CalendarAttendeeAggregate::retrieve($id)
            ->create($data)
            ->persist();

        return CalendarAttendee::findOrFail($id);
    }
}
