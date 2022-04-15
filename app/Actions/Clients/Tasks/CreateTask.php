<?php

namespace App\Actions\Clients\Tasks;

//use App\Aggregates\Clients\CalendarAggregate;
use App\Aggregates\Users\UserAggregate;

use App\Helpers\Uuid;
//use App\Models\Calendar\CalendarEvent;
//use App\Models\Calendar\CalendarEventType;
//use App\Models\Endusers\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class CreateTask
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
/*
 *
user_id: id
due_at: timestamp - nullable
completed_at - timestamp - nullable
timestamps
softdeletes
 *
 */

        return [
            'title' =>['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'user_id' => ['sometimes'],
            'due_at' => ['sometimes'],
            'completed_at' => ['sometimes'],
        ];
    }

    public function handle($data, $user = null)
    {
        $id = Uuid::new();
        $data['id'] = $id;


        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;; //Pulling eventType color for this table because that's how fullCalender.IO wants it

        if(isset($user->id))
            $data['user_attendees'][] = $user->id; //If you make the event, you're automatically an attendee.

        if(!is_null($data['user_attendees'])) {
            $data['user_attendees'] = array_values(array_unique($data['user_attendees'])); //This will dupe check and then re-index the array.
            foreach($data['user_attendees'] as $user) {
                $user = User::whereId($user)->select('id', 'name', 'email')->first();
                if($user) {
                    CalendarAggregate::retrieve($data['client_id'])
                        ->addCalendarAttendee($user->id ?? "Auto Generated",
                            [
                        'entity_type' => User::class,
                        'entity_id' => $user->id,
                        'entity_data' => $user,
                        'calendar_event_id' => $id,
                        'invitation_status' => 'Invitation Pending'
                        ])->persist();
                }
            }
        }

        if(!empty($data['lead_attendees'])) {
            $data['lead_attendees'] = array_values(array_unique($data['lead_attendees'])); //This will dupe check and then re-index the array.
            foreach($data['lead_attendees'] as $lead) {
                $lead = Lead::whereId($lead)->select('id', 'first_name', 'last_name', 'email')->first();
                if($lead) {
                    CalendarAggregate::retrieve($data['client_id'])
                        ->addCalendarAttendee($user->id ?? "Auto Generated",
                            [
                                'entity_type' => Lead::class,
                                'entity_id' => $lead->id,
                                'entity_data' => $lead,
                                'calendar_event_id' => $data['id'],
                                'invitation_status' => 'Invitation Pending'
                            ])->persist();
                }
            }
        }

        unset($data['user_attendees']);
        unset($data['lead_attendees']);


        UserAggregate::retrieve($data['client_id'])
            ->createTask($user->id ?? "Auto Generated", $data)
            ->persist();

        return UserAggregate::findOrFail($id);
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

        return Redirect::back();
    }

}
