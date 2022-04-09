<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\Calendar\CalendarAttendee;
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
        //Pulling eventType color for this table because that's how fullCalender.IO wants it
        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;

        $userAttendeeIDs = [];
        $leadAttendeeIDs = [];
        $currentAttendees = CalendarAttendee::whereCalendarEventId($data['id'])->get()->toArray();
        foreach($currentAttendees as $item) {
            if($item['entity_type'] == User::class)
            {
                $userAttendeeIDs[] = $item['entity_id'];
            }
            if($item['entity_type'] == Lead::class)
            {
                $leadAttendeeIDs[] = $item['entity_id'];
            }
        }

        if(!empty($data['attendees'])) {
            $data['attendees'] = array_values(array_unique($data['attendees'])); //This will dupe check and then re-index the array.
            //Delete Users
            $delete = array_merge(array_diff($data['attendees'], $userAttendeeIDs), array_diff($userAttendeeIDs, $data['attendees']));
            foreach($delete as $user)
            {
                CalendarAggregate::retrieve($data['client_id'])
                    ->deleteCalendarAttendee($user, ['entity_type' => User::class, 'entity_id' => $user])
                    ->persist();
            }
            //Add Users
            foreach($data['attendees'] as $user)
            {
                if(!in_array($user, $userAttendeeIDs)) {
                    $user = User::whereId($user)->select('id', 'name', 'email')->first();
                    if($user) {
                        CalendarAggregate::retrieve($data['client_id'])
                            ->addCalendarAttendee($user->id ?? "Auto Generated",
                                [
                                    'entity_type' => User::class,
                                    'entity_id' => $user->id,
                                    'entity_data' => $user,
                                    'calendar_event_id' => $data['id'],
                                    'invitation_status' => 'sent' // TODO add send notification function here and the result is the status
                                ])->persist();
                    }
                }
            }
        }

        if(!empty($data['lead_attendees'])) {
            //This will dupe check and then re-index the array.
            $data['lead_attendees'] = array_values(array_unique($data['lead_attendees']));
            //Delete Users
            $delete = array_merge(array_diff($data['lead_attendees'], $leadAttendeeIDs), array_diff($leadAttendeeIDs, $data['lead_attendees']));
            foreach($delete as $lead)
            {
                CalendarAggregate::retrieve($data['client_id'])
                    ->deleteCalendarAttendee($lead, ['entity_type' => Lead::class, 'entity_id' => $lead])
                    ->persist();
            }
            //Add Users
            foreach($data['lead_attendees'] as $lead)
            {
                if(!in_array($user, $leadAttendeeIDs)) {
                    $lead = Lead::whereId($lead)->select('id', 'first_name', 'last_name', 'email')->first();
                    if($lead) {
                        CalendarAggregate::retrieve($data['client_id'])
                            ->addCalendarAttendee($user->id ?? "Auto Generated",
                                [
                                    'entity_type' => Lead::class,
                                    'entity_id' => $lead->id,
                                    'entity_data' => $lead,
                                    'calendar_event_id' => $data['id'],
                                    'invitation_status' => 'sent' // TODO add send notification function here and the result is the status
                                ])->persist();
                    }
                }
            }
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
