<?php

declare(strict_types=1);

namespace App\Domain\CalendarAttendees\Actions;

use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class AcceptInvite
{
    use AsAction;

    public function handle(CalendarAttendee $calendarAttendee): CalendarAttendee
    {
        CalendarAttendeeAggregate::retrieve($calendarAttendee->id)
            ->accept()
            ->persist();

        return $calendarAttendee->refresh();
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $_): bool
    {
        return true;
    }

    public function asController(CalendarAttendee $calendarAttendee): CalendarAttendee
    {
        return $this->handle(
            $calendarAttendee
        );
    }

    public function htmlResponse(CalendarAttendee $calendarAttendee): RedirectResponse
    {
        Alert::success("Invitation Accepted!")->flash();

        return Redirect::back();
    }
}
