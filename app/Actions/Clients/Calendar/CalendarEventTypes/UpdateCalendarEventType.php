<?php

namespace App\Actions\Clients\Calendar\CalendarEventTypes;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
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
    public function rules()
    {
        return [
            'name' => ['sometimes', 'required', 'string','max:50'],
            'description' => ['sometimes', 'string', 'nullable'],
            'type' => ['sometimes', 'required', 'string', 'nullable'],
            'color' => ['sometimes', 'required', 'string'],
        ];
    }

    public function handle($data, $user = null)
    {
        CalendarAggregate::retrieve($data['client_id'])
            ->updateCalendarEventType($user->id ?? "Auto Generated", $data)
            ->persist();

        return CalendarEventType::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['client_id'] = $request->user()->client_id;
        $calendar_event_type = $this->handle(
            $data
        );

        Alert::success("Calendar Event Type '{$calendar_event_type->name}' was updated")->flash();

        return Redirect::route('calendar.event_types.edit', $calendar_event_type->id);
    }
}
