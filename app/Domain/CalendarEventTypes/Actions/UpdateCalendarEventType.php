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

class UpdateCalendarEventType
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string','max:50'],
            'description' => ['sometimes', 'string', 'nullable'],
            'type' => ['sometimes', 'required', 'string', 'nullable'],
            'color' => ['sometimes', 'required', 'string'],
        ];
    }

    public function handle(CalendarEventType $calendarEventType, array $data): CalendarEventType
    {
        CalendarEventTypeAggregate::retrieve($calendarEventType->id)
            ->update($data)
            ->persist();

        return $calendarEventType->refresh();
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

    public function asController(ActionRequest $request, CalendarEventType $calendarEventType): CalendarEventType
    {
        $data = $request->validated();

        return $this->handle(
            $calendarEventType,
            $data
        );
    }

    public function htmlResponse(CalendarEventType $calendar_event_type): RedirectResponse
    {
        Alert::success("Calendar Event Type '{$calendar_event_type->name}' was updated")->flash();

        return Redirect::route('calendar.event_types.edit', $calendar_event_type->id);
    }
}
