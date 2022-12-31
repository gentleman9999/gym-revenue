<?php

namespace App\Domain\CalendarEvents\Actions;

use App\Domain\CalendarAttendees\Actions\AddCalendarAttendee;
use App\Domain\CalendarAttendees\Actions\RemoveCalendarAttendee;
use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEvents\CalendarEventAggregate;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
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
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:50'],
            'description' => ['sometimes', 'string', 'nullable'],
            'full_day_event' => ['sometimes', 'required', 'boolean'],
            'start' => ['sometimes', 'required'],
            'end' => ['sometimes', 'required'],
            'event_type_id' => ['sometimes', 'exists:calendar_event_types,id'],
            'user_attendees' => ['sometimes', 'array'],
            'lead_attendees' => ['sometimes', 'array'],
            'member_attendees' => ['sometimes', 'array'],
            'my_reminder' => ['sometimes', 'int'],
            'location_id' => ['sometimes', 'string'],
        ];
    }

    public function handle(CalendarEvent $calendarEvent, array $data): CalendarEvent
    {
        $event_type_id = $data['event_type_id'] ?? $calendarEvent->event_type_id;
        $calendarEventType = CalendarEventType::whereId($event_type_id)->select('type')->first();
        $is_task = $calendarEventType->type === 'Task' ?? false;
        //Pulling eventType color for this table because that's how fullCalender.IO wants it
        if (array_key_exists('event_type_id', $data)) {
            $data['color'] = $calendarEventType->color;
        }
        CalendarEventAggregate::retrieve($calendarEvent->id)
            ->update($data)
            ->persist();
        if (array_key_exists('my_reminder', $data)) {
            $reminderData = Reminder::whereEntityType(CalendarEvent::class)
                ->whereEntityId($calendarEvent->id)
                ->whereUserId(auth()->user()->id)
                ->first();
            $reminderData->remind_time = $data['my_reminder'];
            UserAggregate::retrieve(auth()->user()->id)->updateReminder($reminderData->toArray())->persist();
        }

        $userAttendeeIDs = [];
        $leadAttendeeIDs = [];
        $memberAttendeeIDs = [];
        $currentAttendees = CalendarAttendee::whereCalendarEventId($calendarEvent->id)->get()->toArray();
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
                foreach ($delete as $user_id) {
                    $calendarAttendee = CalendarAttendee::whereCalendarEventId($calendarEvent->id)->whereEntityType(User::class)->whereEntityId($user_id)->first();
                    if ($calendarAttendee) {
                        RemoveCalendarAttendee::run($calendarAttendee);
                    }
                }
                //Add Users
                foreach ($data['user_attendees'] as $user) {
                    if (! in_array($user, $userAttendeeIDs)) {
                        $user = User::whereId($user)->select('id', 'email')->first();
                        if ($user) {
                            AddCalendarAttendee::run([
                                'entity_type' => User::class,
                                'entity_id' => $user->id,
                                'entity_data' => $user,
                                'calendar_event_id' => $calendarEvent->id,
                                'invitation_status' => 'Invitation Pending',
                                'is_task' => $is_task,
                            ]);
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
                foreach ($delete as $lead_id) {
                    $calendarAttendee = CalendarAttendee::whereCalendarEventId($calendarEvent->id)->whereEntityType(Lead::class)->whereEntityId($lead_id)->first();
                    if ($calendarAttendee) {
                        RemoveCalendarAttendee::run($calendarAttendee);
                    }
                }
                //Add Users
                foreach ($data['lead_attendees'] as $lead_id) {
                    if (! in_array($lead_id, $leadAttendeeIDs)) {
                        $lead = Lead::whereId($lead_id)->select('id', 'first_name', 'last_name', 'email')->first();
                        if ($lead) {
                            AddCalendarAttendee::run([
                                'entity_type' => Lead::class,
                                'entity_id' => $lead->id,
                                'entity_data' => $lead->id,
                                'calendar_event_id' => $calendarEvent->id,
                                'invitation_status' => 'Invitation Pending',
                                'is_task' => $is_task,
                            ]);
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
                foreach ($delete as $member_id) {
                    $calendarAttendee = CalendarAttendee::whereCalendarEventId($calendarEvent->id)->whereEntityType(Member::class)->whereEntityId($member_id)->first();
                    if ($calendarAttendee) {
                        RemoveCalendarAttendee::run($calendarAttendee);
                    }
                }
                //Add Users
                foreach ($data['member_attendees'] as $member) {
                    if (! in_array($member, $memberAttendeeIDs)) {
                        $member = Member::whereId($member)->select('id', 'first_name', 'last_name', 'email')->first();
                        if ($member) {
                            AddCalendarAttendee::run([
                                'entity_type' => Member::class,
                                'entity_id' => $member->id,
                                'entity_data' => $member,
                                'calendar_event_id' => $calendarEvent->id,
                                'invitation_status' => 'Invitation Pending',
                                'is_task' => $is_task,
                            ]);
                        }
                    }
                }
            }
        }

        if (array_key_exists('event_type_id', $data)) {
            if ($calendarEvent->type == 'Task') {
                //Whoever updated this to a task owns it.
                $data['owner_id'] = $user;

                //We clear out all attendee's for Tasks
                foreach ($userAttendeeIDs as $user_id) {
                    $calendarAttendee = CalendarAttendee::whereCalendarEventId($calendarEvent->id)->whereEntityType(User::class)->whereEntityId($user_id)->first();
                    if ($calendarAttendee) {
                        RemoveCalendarAttendee::run($calendarAttendee);
                    }
                }
            }
        }

        return $calendarEvent->refresh();
    }

    public function __invoke($_, array $args): CalendarEvent
    {
        $event = CalendarEvent::find($args['input']['id']);
        $args['input']['start'] = CarbonImmutable::create($args['input']['start']);
        $args['input']['end'] = CarbonImmutable::create($args['input']['end']);

        return $this->handle($event, $args['input']);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.update', CalendarEvent::class);
    }

    public function asController(ActionRequest $request, CalendarEvent $calendarEvent): CalendarEvent
    {
        $data = $request->validated();

        return $this->handle(
            $calendarEvent,
            $data,
        );
    }

    public function htmlResponse(CalendarEvent $calendarEvent): RedirectResponse
    {
        Alert::success("Calendar Event '{$calendarEvent->title}' was updated")->flash();

        return Redirect::back();
    }
}
