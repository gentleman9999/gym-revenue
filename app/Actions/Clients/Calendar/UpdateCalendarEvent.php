<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Endusers\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

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
            'lead_attendees' => ['sometimes', 'array'],
        ];
    }

    public function handle($data, $user=null)
    {
        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;; //Pulling eventType color for this table because that's how fullCalender.IO wants it

        /** ATTENDEE's -- Prep for DB
         * Creating attendees bucket to hold info that we're prepping
         * Dupe check then reindex array of ID's
         * Add Users to Bucket then encode the var
         */
        $attendees = [];
        if(!empty($data['attendees'])) {
            $data['attendees'] = array_values(array_unique($data['attendees'])); //This will dupe check and then re-index the array.
            foreach($data['attendees'] as $user)
            {
                $user = User::whereId($user)->select('id', 'name', 'email')->first();
                if($user)
                    $attendees[] = $user;
            }
            $data['attendees'] = $attendees;
        }

        /** LEAD ATTENDEE's -- Prep for DB
         * Creating leadAttendees bucket to hold info that we're prepping
         * Dupe check then reindex array of ID's
         * Add Lead Users to Bucket then encode the var
         */
        $leadAttendees = [];
        if(!empty($data['lead_attendees'])) {
            $data['lead_attendees'] = array_values(array_unique($data['lead_attendees'])); //This will dupe check and then re-index the array.
            foreach($data['lead_attendees'] as $user)
            {
                $lead = Lead::whereId($user)->select('id', 'first_name', 'last_name', 'email')->first();
                if($user)
                    $leadAttendees[] = $lead;
            }
            $data['lead_attendees'] = $leadAttendees;
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
