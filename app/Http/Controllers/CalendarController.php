<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Endusers\Member;
use App\Models\Reminder;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        $locations = Location::whereClientId($client_id)->get();
        $currentLocationSelect = Location::find($request->user()->current_location_id);

        if ($request->get('start')) {
            if (is_null($currentLocationSelect)) {
                $eventsForTeam = CalendarEvent::whereClientId($client_id)
                    ->with('type', 'attendees', 'files')
                    ->filter($request->only('search', 'start', 'end', 'viewUser'))
                    ->get();
            } else {
                $eventsForTeam = CalendarEvent::whereClientId($client_id)
                    ->whereLocationId($currentLocationSelect->id)
                    ->with('type', 'attendees', 'files')
                    ->filter($request->only('search', 'start', 'end', 'viewUser'))
                    ->get();
            }
        } else {
            $eventsForTeam = [];
        }

        foreach ($eventsForTeam as $key => $event) {
            $user_attendees = [];
            $lead_attendees = [];
            $member_attendees = [];
            if ($event->attendees) {
                foreach ($event->attendees as $attendee) {
                    if ($attendee->entity_type == User::class) {
                        if (request()->user()->id == $attendee->entity_id) {
                            $eventsForTeam[$key]['my_reminder'] = Reminder::whereEntityType(CalendarEvent::class)
                                ->whereEntityId($event['id'])
                                ->whereUserId($attendee->entity_id)
                                ->first();

                            $eventsForTeam[$key]['im_attending'] = true;
                        }
                        $user_attendees[] = [
                            'id' => (int)$attendee->entity_id,
                            'reminder' => Reminder::whereEntityType(CalendarEvent::class)
                                ->whereEntityId($event['id'])
                                ->whereUserId($attendee->entity_id)
                                ->first() ?? null,
                        ];
                    }
                    if ($attendee->entity_type == Lead::class) {
                        $lead_attendees[]['id'] = $attendee->entity_id;
                    }
                    if ($attendee->entity_type == Member::class) {
                        $member_attendees[]['id'] = $attendee->entity_id;
                    }
                }
            }
            $eventsForTeam[$key]->user_attendees = $user_attendees;
            $eventsForTeam[$key]->lead_attendees = $lead_attendees;
            $eventsForTeam[$key]->member_attendees = $member_attendees;

            $eventsForTeam[$key]->event_owner = User::whereId($event['owner_id'])->first() ?? null;
        }

        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            $is_default_team = $client->default_team_name->value == $current_team->id;

            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = User::whereClientId($client_id)->get();
            } else {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids = [];
                foreach ($team_users as $team_user) {
                    $user_ids[] = $team_user->user_id;
                }
                $users = User::whereIn('id', $user_ids)
                    ->get();
            }
        }


        return Inertia::render('Calendar/Show', [
            'calendar_events' => $eventsForTeam,
            'calendar_event_types' => CalendarEventType::whereClientId($client_id)->get(),
            'client_id' => $client_id,
            'client_users' => $users,
            'lead_users' => Lead::whereClientId($client_id)->select('id', 'first_name', 'last_name')->get(),
            'member_users' => Member::whereClientId($client_id)->select('id', 'first_name', 'last_name')->get(),
            'locations' => $locations,
        ]);
    }

    public function QuickView(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }
        $locations = Location::whereClientId($client_id)->get();
        $eventsByLocation = [];

        foreach ($locations as $key => $location) {
            if ($request->get('start')) {
                $eventsForTeam = CalendarEvent::whereClientId($client_id)
                    ->whereLocationId($location->id)
                    ->with('type', 'attendees', 'files')
                    ->filter($request->only('search', 'start', 'end', 'viewUser'))
                    ->get();
            } else {
                $eventsForTeam = [];
            }
            $eventsByLocation[$key] = $eventsForTeam;
        }

        return Inertia::render('Calendar/QuickView', [
            'calendar_events_by_locations' => $eventsByLocation,
        ]);
    }

    public function eventTypes(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        $event_types = CalendarEventType::whereClient_id($client_id)
            ->filter($request->only('search', 'trashed', 'type'))
            ->sort()
            ->paginate(10)
            ->appends(request()->except('page'));

        return Inertia::render('Calendar/EventTypes/Show', [
            'calendarEventTypes' => $event_types,
        ]);
    }

    public function createEventType(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        return Inertia::render('Calendar/EventTypes/Create', [
        ]);
    }

    public function editEventType(Request $request, $id)
    {
//        $client_id = request()->user()->currentClientId();
        $calendarEventType = CalendarEventType::findOrFail($id);

        return Inertia::render('Calendar/EventTypes/Edit', [
            'calendarEventType' => $calendarEventType,
        ]);
    }
}
