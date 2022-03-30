<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCalendarEvent
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
            'client_id' => ['required', 'exists:clients,id'],
            'attendees' => ['sometimes', 'array'],
        ];
    }

    public function handle($data, $user=null)
    {
        $eventType = CalendarEventType::whereId($data['event_type_id'])->get();
        $data['color'] = $eventType->first()->color;

        $attendees = [];
        if(!is_null($data['attendees'])) {
            foreach($data['attendees'] as $user) {
                $attendees[] = User::whereId($user)->select('id', 'name', 'email')->first();
            }
            $data['attendees'] = json_encode($attendees);
        }


        CalendarAggregate::retrieve($data['client_id'])
            ->updateCalendarEvent($user->id ?? "Auto Generated" , $data)
            ->persist();

        return CalendarEvent::find($data['id']);
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
        $calendar = $this->handle(
            $data
        );

        Alert::success("Calendar Event '{$calendar->title}' was updated")->flash();

        return Redirect::back();
    }

}
