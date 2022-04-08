<?php

namespace App\Actions\Clients\Calendar\CalendarEventTypes;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use Bouncer;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreCalendarEventType
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }

    public function handle($id, $user=null)
    {
        $client_id = CalendarEventType::withTrashed()->findOrFail($id)->client->id;
        CalendarAggregate::retrieve( $client_id)->restoreCalendarEventType($user->id ?? "Auto Generated", $id)->persist();
        return CalendarEventType::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.restore', CalendarEvent::class);
    }

    public function asController(ActionRequest $request, $id)
    {

        $data = $request->validated();
        $calendar_event_type = $this->handle(
            $id
        );

        Alert::success("Calendar Event Type '{$calendar_event_type->name}' restored.")->flash();

        return Redirect::back();
    }
}
