<?php

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

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
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
