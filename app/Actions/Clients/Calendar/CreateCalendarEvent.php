<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Helpers\Uuid;
use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;


class CreateCalendarEvent
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
            'title' =>['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'full_day_event' => ['required', 'boolean'],
            'start' => ['required'],
            'end' => ['required'],
            'event_type_id' => ['required', 'exists:calendar_event_types,id'],
            'client_id' => ['required', 'exists:clients,id']
        ];
    }

    public function handle($data, $user = null)
    {

        $eventType = CalendarEventType::whereId($data['event_type_id'])->get();
        $id = Uuid::new();
        $data['id'] = $id;
        $data['color'] = $eventType->first()->color;
//        dd($data);
        CalendarAggregate::retrieve($data['client_id'])
            ->createCalendarEvent($user->id ?? "Auto Generated", $data)
            ->persist();

        return CalendarEvent::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('calendar.create', CalendarEvent::class);
    }

    public function asController(ActionRequest $request)
    {

        $calendar = $this->handle(
            $request->validated(),
            $request->user()
        );

        Alert::success("Calendar Event '{$calendar->title}' was created")->flash();

//        return Redirect::route('calendar');
        return Redirect::back();
    }

}
