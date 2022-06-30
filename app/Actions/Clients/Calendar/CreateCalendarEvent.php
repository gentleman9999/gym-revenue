<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Helpers\Uuid;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Endusers\Lead;
use App\Models\Endusers\Member;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

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
            'title' => ['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'full_day_event' => ['required', 'boolean'],
            'start' => ['required'],
            'end' => ['required'],
            'event_type_id' => ['required', 'exists:calendar_event_types,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'user_attendees' => ['sometimes'],
            'lead_attendees' => ['sometimes'],
            'member_attendees' => ['sometimes'],
        ];
    }

    public function handle($data, $user = null)
    {
        $id = Uuid::new();
        $data['id'] = $id;
        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;
        ; //Pulling eventType color for this table because that's how fullCalender.IO wants it

        if (isset($user->id)) {
            $data['user_attendees'][] = $user->id;
        } //If you make the event, you're automatically an attendee.

        if (! is_null($data['user_attendees'])) {
            $data['user_attendees'] = array_values(array_unique($data['user_attendees'])); //This will dupe check and then re-index the array.
            foreach ($data['user_attendees'] as $user) {
                $user = User::findOrFail($user);
                if ($user) {
                    CalendarAggregate::retrieve($data['client_id'])
                        ->addCalendarAttendee(
                            $user->id ?? "Auto Generated",
                            [
                        'entity_type' => User::class,
                        'entity_id' => $user->id,
                        'entity_data' => $user,
                        'calendar_event_id' => $id,
                        'invitation_status' => 'Invitation Pending',
                        ]
                        )->persist();
                }
            }
        }

        if (! empty($data['lead_attendees'])) {
            $data['lead_attendees'] = array_values(array_unique($data['lead_attendees'])); //This will dupe check and then re-index the array.
            foreach ($data['lead_attendees'] as $lead) {
                $lead = Lead::whereId($lead)->select('id', 'first_name', 'last_name', 'email')->first();
                if ($lead) {
                    CalendarAggregate::retrieve($data['client_id'])
                        ->addCalendarAttendee(
                            $user->id ?? "Auto Generated",
                            [
                                'entity_type' => Lead::class,
                                'entity_id' => $lead->id,
                                'entity_data' => $lead,
                                'calendar_event_id' => $data['id'],
                                'invitation_status' => 'Invitation Pending',
                            ]
                        )->persist();
                }
            }
        }

        if (! empty($data['member_attendees'])) {
            $data['member_attendees'] = array_values(array_unique($data['member_attendees'])); //This will dupe check and then re-index the array.
            foreach ($data['member_attendees'] as $member) {
                $member = Member::whereId($member)->select('id', 'first_name', 'last_name', 'email')->first();
                if ($member) {
                    CalendarAggregate::retrieve($data['client_id'])
                        ->addCalendarAttendee(
                            $user->id ?? "Auto Generated",
                            [
                                'entity_type' => Member::class,
                                'entity_id' => $member->id,
                                'entity_data' => $member,
                                'calendar_event_id' => $data['id'],
                                'invitation_status' => 'Invitation Pending',
                            ]
                        )->persist();
                }
            }
        }

        unset($data['user_attendees']);
        unset($data['lead_attendees']);
        unset($data['member_attendees']);

        if ($user) {
            $data['owner_id'] = $user->id;
        }

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

        return Redirect::back();
    }
}
