<?php

namespace App\Domain\CalendarAttendees\Actions;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendeeAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeclineInvite
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

    public function asController(ActionRequest $request): CalendarAttendee
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(CalendarAttendee $calendarAttendee): RedirectResponse
    {
        Alert::success("Invitation Declined!")->flash();

        return Redirect::back();
    }
}
