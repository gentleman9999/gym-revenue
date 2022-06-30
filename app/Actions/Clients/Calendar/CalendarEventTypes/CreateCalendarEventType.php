<?php

namespace App\Actions\Clients\Calendar\CalendarEventTypes;

use App\Aggregates\Clients\CalendarAggregate;
use App\Helpers\Uuid;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateCalendarEventType
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
            'name' => ['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'type' => ['required', 'string', 'nullable'],
            'color' => ['required', 'string'],
            'client_id' => ['required', 'exists:clients,id'],
        ];
    }

    public function handle($data, $user = null)
    {
        $id = Uuid::new();
        $data['id'] = $id;

        CalendarAggregate::retrieve($data['client_id'])
            ->createCalendarEventType($user->id ?? "Auto Generated", $data)
            ->persist();

        return CalendarEventType::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.create', CalendarEvent::class);
    }

    public function asController(ActionRequest $request)
    {
        $calendar_event_type = $this->handle(
            $request->validated(),
            $request->user()
        );

        Alert::success("Calendar Event Type '{$calendar_event_type->name}' was created")->flash();

        return Redirect::route('calendar.event_types.edit', $calendar_event_type->id);
    }
}
