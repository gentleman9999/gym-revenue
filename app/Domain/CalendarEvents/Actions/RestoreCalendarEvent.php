<?php

namespace App\Domain\CalendarEvents\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEvents\CalendarEventAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreCalendarEvent
{
    use AsAction;

    public function handle(CalendarEvent $calendarEvent): CalendarEvent
    {
        CalendarEventAggregate::retrieve($calendarEvent->id)->restore()->persist();

        return $calendarEvent->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.restore', CalendarEvent::class);
    }

    public function asController(CalendarEvent $calendarEvent): CalendarEvent
    {
        return $this->handle(
            $calendarEvent,
        );
    }

    public function htmlResponse(CalendarEvent $calendarEvent): RedirectResponse
    {
        Alert::success("Calendar Event '{$calendarEvent->title}' restored.")->flash();

        return Redirect::back();
    }
}
