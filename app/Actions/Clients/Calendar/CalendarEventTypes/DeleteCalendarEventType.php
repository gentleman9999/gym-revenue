<?php

namespace App\Actions\Clients\Calendar\CalendarEventTypes;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\Clients\Location;
use Bouncer;
use App\Actions\Fortify\PasswordValidationRules;
use App\Aggregates\Clients\ClientAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteCalendarEventType
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
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($client_id, $id, $user=null)
    {
        $calendar_event_type = CalendarEvent::findOrFail($id);
        CalendarAggregate::retrieve($client_id)->deleteCalendarEventType($user->id ?? "Auto Generated", $id)->persist();
        return $calendar_event_type;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $calendar_event_type = $this->handle(
            $data['client_id'],
            $id
        );

        Alert::success("Calendar Event Type '{$calendar_event_type->name}' was deleted")->flash();

        return Redirect::back();
    }
}
