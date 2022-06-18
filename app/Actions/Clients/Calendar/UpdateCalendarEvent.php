<?php

namespace App\Actions\Clients\Calendar;

use App\Aggregates\Clients\CalendarAggregate;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\User;
use App\Domain\Users\UserAggregate;
use App\Models\Calendar\CalendarAttendee;
use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Endusers\Lead;
use App\Models\Endusers\Member;
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
            'title' => ['required', 'string','max:50'],
            'description' => ['string', 'nullable'],
            'full_day_event' => ['required', 'boolean'],
            'start' => ['required'],
            'end' => ['required'],
            'event_type_id' => ['required', 'exists:calendar_event_types,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'user_attendees' => ['sometimes', 'array'],
            'lead_attendees' => ['sometimes', 'array'],
            'member_attendees' => ['sometimes', 'array'],
            'my_reminder' => ['sometimes', 'int'],
        ];
    }

    public function handle($data, $user)
    {
        //Pulling eventType color for this table because that's how fullCalender.IO wants it
        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;

        if (array_key_exists('my_reminder', $data)) {
            $reminderData = Reminder::whereEntityType(CalendarEvent::class)
                ->whereEntityId($data['id'])
                ->whereUserId($user->id)
                ->first();
            $reminderData->remind_time = $data['my_reminder'];
            UserAggregate::retrieve($user->id)->updateReminder($user->id, $reminderData->toArray())->persist();
        }
        $userAttendeeIDs = [];
        $leadAttendeeIDs = [];
        $memberAttendeeIDs = [];
        $currentAttendees = CalendarAttendee::whereCalendarEventId($data['id'])->get()->toArray();
        foreach ($currentAttendees as $item) {
            if ($item['entity_type'] == User::class) {
                $userAttendeeIDs[] = $item['entity_id'];
            }
            if ($item['entity_type'] == Lead::class) {
                $leadAttendeeIDs[] = $item['entity_id'];
            }
            if ($item['entity_type'] == Member::class) {
                $memberAttendeeIDs[] = $item['entity_id'];
            }
        }


        if (array_key_exists('user_attendees', $data)) {
            if (! empty($data['user_attendees']) || ! empty($userAttendeeIDs)) {
                $data['user_attendees'] = array_values(array_unique($data['user_attendees'])); //This will dupe check and then re-index the array.
                //Delete Users
                $delete = array_merge(array_diff($data['user_attendees'], $userAttendeeIDs), array_diff($userAttendeeIDs, $data['user_attendees']));
                foreach ($delete as $user) {
                    CalendarAggregate::retrieve($data['client_id'])
                            ->deleteCalendarAttendee($user, ['entity_type' => User::class, 'entity_id' => $user, 'event_id' => $data['id']])
                            ->persist();
                }
                //Add Users
                foreach ($data['user_attendees'] as $user) {
                    if (! in_array($user, $userAttendeeIDs)) {
                        $user = User::whereId($user)->select('id', 'name', 'email')->first();
                        if ($user) {
                            CalendarAggregate::retrieve($data['client_id'])
                                    ->addCalendarAttendee(
                                        $user->id ?? "Auto Generated",
                                        [
                                            'entity_type' => User::class,
                                            'entity_id' => $user->id,
                                            'entity_data' => $user,
                                            'calendar_event_id' => $data['id'],
                                            'invitation_status' => 'Invitation Pending',
                                            'is_task' => CalendarEventType::whereId($data['event_type_id'])->select('type')->first()->type == 'Task' ?? false,
                                        ]
                                    )->persist();
                        }
                    }
                }
            }
        }

        if (array_key_exists('lead_attendees', $data)) {
            if (! empty($data['lead_attendees']) || ! empty($leadAttendeeIDs)) {
                //This will dupe check and then re-index the array.
                $data['lead_attendees'] = array_values(array_unique($data['lead_attendees']));
                //Delete Users
                $delete = array_merge(array_diff($data['lead_attendees'], $leadAttendeeIDs), array_diff($leadAttendeeIDs, $data['lead_attendees']));
                foreach ($delete as $lead) {
                    CalendarAggregate::retrieve($data['client_id'])
                            ->deleteCalendarAttendee($lead, ['entity_type' => Lead::class, 'entity_id' => $lead, 'event_id' => $data['id']])
                            ->persist();
                }
                //Add Users
                foreach ($data['lead_attendees'] as $lead) {
                    if (! in_array($lead, $leadAttendeeIDs)) {
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
                                            'is_task' => CalendarEventType::whereId($data['event_type_id'])->select('type')->first()->type == 'Task' ?? false,
                                        ]
                                    )->persist();
                        }
                    }
                }
            }
        }

        if (array_key_exists('member_attendees', $data)) {
            if (! empty($data['member_attendees']) || ! empty($memberAttendeeIDs)) {
                //This will dupe check and then re-index the array.
                $data['member_attendees'] = array_values(array_unique($data['member_attendees']));
                //Delete Users
                $delete = array_merge(array_diff($data['member_attendees'], $memberAttendeeIDs), array_diff($memberAttendeeIDs, $data['member_attendees']));
                foreach ($delete as $member) {
                    CalendarAggregate::retrieve($data['client_id'])
                            ->deleteCalendarAttendee($member, ['entity_type' => Member::class, 'entity_id' => $member, 'event_id' => $data['id']])
                            ->persist();
                }
                //Add Users
                foreach ($data['member_attendees'] as $member) {
                    if (! in_array($member, $memberAttendeeIDs)) {
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
                                            'is_task' => CalendarEventType::whereId($data['event_type_id'])->select('type')->first()->type == 'Task' ?? false,
                                        ]
                                    )->persist();
                        }
                    }
                }
            }
        }

        if (CalendarEventType::whereId($data['event_type_id'])->select('type')->first()->type == 'Task') {
            //Whoever updated this to a task owns it.
            $data['owner_id'] = $user->id;

            //We clear out all attendee's for Tasks
            foreach ($userAttendeeIDs as $user) {
                CalendarAggregate::retrieve($data['client_id'])
                    ->deleteCalendarAttendee($user, ['entity_type' => User::class, 'entity_id' => $user, 'event_id' => $data['id']])
                    ->persist();
            }
        }


        CalendarAggregate::retrieve($data['client_id'])
            ->updateCalendarEvent($user->id ?? "Auto Generated", $data)
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
            $data,
            $request->user()
        );

        Alert::success("Calendar Event '{$calendar->title}' was updated")->flash();

        return Redirect::back();
    }
}
