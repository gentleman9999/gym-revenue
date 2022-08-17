<?php

namespace App\Domain\CalendarEventTypes\Actions;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\CalendarEventTypes\CalendarEventTypeAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteCalendarEventType
{
    use AsAction;

    public function handle(CalendarEventType $calendarEventType): CalendarEventType
    {
        CalendarEventTypeAggregate::retrieve($calendarEventType->id)->delete()->persist();

        return $calendarEventType;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(CalendarEventType $calendarEventType): CalendarEventType
    {
        return $this->handle(
            $calendarEventType
        );
    }

    public function htmlResponse(CalendarEventType $calendar_event_type): RedirectResponse
    {
        Alert::success("Calendar Event Type '{$calendar_event_type->name}' was deleted")->flash();

        return Redirect::back();
    }
}
