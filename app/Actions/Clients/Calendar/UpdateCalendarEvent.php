<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
use App\Models\Endusers\Lead;
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
            'lead_attendees' => ['sometimes', 'array'],
        ];
    }

    public function handle($data, $user=null)
    {
        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;; //Pulling eventType color for this table because that's how fullCalender.IO wants it
        $currentEvent = CalendarEvent::whereId($data['id'])->first(); //Grabbing current info b4 we morph it

        /** ATTENDEE's -- Find Current
         * Lookup what client users were already assigned to this event
         * Then we morph the data into what we want while
         * adding it to the existing data in the ['attendees'] var
         */
        $currentUsers = json_decode($currentEvent->attendees);
        if(!is_null($currentUsers)) {
            foreach ($currentUsers as $u) {
                $data['attendees'][] = $u->id;
            }
        }

        /** ATTENDEE's -- Prep for DB
         * Creating attendees bucket to hold info that we're prepping for a json_encode
         * Dupe check then reindex array of ID's
         * Add Users to Bucket then encode the var
         */
        $attendees = [];
        if(!empty($data['attendees'])) {
            foreach ($data['attendees'] as $key => $value)
            {
                if(isset($value['id'])) {
                    $data['attendees'][] = $value['id'];
                    unset($data['attendees'][$key]);
                }

            }
            $data['attendees'] = array_values(array_unique($data['attendees'])); //This will dupe check and then re-index the array.
            foreach($data['attendees'] as $user) {
                $attendees[] = User::whereId($user)->select('id', 'name', 'email')->first();
            }
            $data['attendees'] = json_encode($attendees);
        }

        /** LEAD ATTENDEE's -- Find Current
         * Lookup what lead users were already assigned to this event
         * Then we morph the data into what we want while
         * adding it to the existing data in the ['lead_attendees'] var
         */
        $currentLeadUsers = json_decode($currentEvent->lead_attendees);
        if(!is_null($currentLeadUsers)) {
            foreach ($currentLeadUsers as $u)
            {
                $data['lead_attendees'][] = $u->id;
            }
        }

        /** LEAD ATTENDEE's -- Prep for DB
         * Creating leadAttendees bucket to hold info that we're prepping for a json_encode
         * Dupe check then reindex array of ID's
         * Add Lead Users to Bucket then encode the var
         */
        $leadAttendees = [];
        if(!empty($data['lead_attendees'])) {
            foreach ($data['lead_attendees'] as $key => $value)
            {
                if(isset($value['id'])) {
                    $data['lead_attendees'][] = $value['id'];
                    unset($data['lead_attendees'][$key]);
                }

            }
            $data['lead_attendees'] = array_values(array_unique($data['lead_attendees'])); //This will dupe check and then re-index the array.
            foreach($data['lead_attendees'] as $user) {
                $leadAttendees[] = Lead::whereId($user)->select('id', 'first_name', 'last_name', 'email')->first();
            }
            $data['lead_attendees'] = json_encode($leadAttendees);
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
