<?php

namespace App\Domain\CalendarEvents\Actions;

use App\Domain\CalendarAttendees\Actions\AddCalendarAttendee;
use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEvents\CalendarEventAggregate;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
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
            'owner_id' => ['sometimes'],
//            'client_id' => ['required', 'exists:clients,id'],
            'user_attendees' => ['sometimes'],
            'lead_attendees' => ['sometimes'],
            'member_attendees' => ['sometimes'],
            'location_id' => ['required', 'string'],
        ];
    }


    public function handle($data, ?User $user = null): CalendarEvent

    {
        $id = Uuid::new();
        $data['color'] = CalendarEventType::whereId($data['event_type_id'])->first()->color;
        //Pulling eventType color for this table because that's how fullCalender.IO wants it
        if ($user) {
            $data['owner_id'] = $user->id;
        }

        if ($user) {
            $data['owner_id'] = $user->id;
        }

        CalendarEventAggregate::retrieve($id)
            ->create($data)
            ->persist();

        $calendarEvent = CalendarEvent::findOrFail($id);

        if (isset($user->id)) {
            $data['user_attendees'][] = $user->id;
        } //If you make the event, you're automatically an attendee.

        if (! is_null($data['user_attendees'])) {
            $data['user_attendees'] = array_values(array_unique($data['user_attendees'])); //This will dupe check and then re-index the array.
            foreach ($data['user_attendees'] as $user) {
                $user = User::find($user);
                if ($user) {
                    AddCalendarAttendee::run([
                        'client_id' => $data['client_id'],
                        'entity_type' => User::class,
                        'entity_id' => $user->id,
                        'entity_data' => $user,
                        'calendar_event_id' => $calendarEvent->id,
                        'invitation_status' => 'Invitation Pending',
                    ]);
                }
            }
        }

        if (! empty($data['lead_attendees'])) {
            $data['lead_attendees'] = array_values(array_unique($data['lead_attendees'])); //This will dupe check and then re-index the array.
            foreach ($data['lead_attendees'] as $lead) {
                $lead = Lead::whereId($lead)->select('id', 'first_name', 'last_name', 'email')->first();
                if ($lead) {
                    AddCalendarAttendee::run([
                        'client_id' => $data['client_id'],
                        'entity_type' => Lead::class,
                        'entity_id' => $lead->id,
                        'entity_data' => $lead,
                        'calendar_event_id' => $calendarEvent->id,
                        'invitation_status' => 'Invitation Pending',
                    ]);
                }
            }
        }

        if (! empty($data['member_attendees'])) {
            $data['member_attendees'] = array_values(array_unique($data['member_attendees'])); //This will dupe check and then re-index the array.
            foreach ($data['member_attendees'] as $member) {
                $member = Member::whereId($member)->select('id', 'first_name', 'last_name', 'email')->first();
                if ($member) {
                    AddCalendarAttendee::run([
                        'client_id' => $data['client_id'],
                        'entity_type' => Member::class,
                        'entity_id' => $member->id,
                        'entity_data' => $member,
                        'calendar_event_id' => $calendarEvent->id,
                        'invitation_status' => 'Invitation Pending',
                    ]);
                }
            }
        }

        unset($data['user_attendees']);
        unset($data['lead_attendees']);
        unset($data['member_attendees']);

        if ((array_key_exists('owner_id', $data) && ! isset($data['owner_id'])) && ! auth()->user()) {
            $data['owner_id'] = auth()->user()->id;
        }

        return $calendarEvent->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('calendar.create', CalendarEvent::class);
    }

    public function asController(ActionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $data['client_id'] = $request->user()->client_id;
        $calendar = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Calendar Event '{$calendar->title}' was created")->flash();

        return Redirect::back();
    }
}
