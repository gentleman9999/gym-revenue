<?php

namespace App\Http\Controllers;

use App\Domain\CalendarEvents\CalendarEvent;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Reminders\Reminder;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamUser;
use App\Domain\Users\Models\Employee;
use App\Domain\Users\Models\Lead;
use App\Domain\Users\Models\Member;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserDetails;
use App\Support\CurrentInfoRetriever;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->client_id;

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        $locations = Location::all();
        //$currentLocationSelect = Location::find($request->user()->current_location_id);

        if ($request->get('start')) {
            //if (is_null($currentLocationSelect)) {
            //    return Redirect::route('calendar.quickview');
            //} else {
            $eventsForTeam = CalendarEvent::whereClientId($client_id)
            //        ->whereLocationId($currentLocationSelect->id)
                    ->with('type', 'attendees', 'files')
                    ->filter($request->only('search', 'start', 'end', 'viewUser'))
                    ->get();
        //}
        } else {
            $eventsForTeam = [];
        }

        foreach ($eventsForTeam as $key => $event) {
            $user_attendees = [];
            $lead_attendees = [];
            $member_attendees = [];
            if ($event->attendees) {
                foreach ($event->attendees as $attendee) {
                    if ($attendee->entity_type == Employee::class) {
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

                        $call_outcome = UserDetails::whereField('call_outcome')
                            ->whereUserId($attendee->entity_id)
                            ->where('misc->entity_id', $event->id)->first();
                    }
                    if ($attendee->entity_type == Member::class) {
                        $member_attendees[]['id'] = $attendee->entity_id;

                        $call_outcome = UserDetails::whereField('call_outcome')
                            ->whereUserId($attendee->entity_id)
                            ->where('misc->entity_id', $event->id)->first();
                    }
                    if ($event->call_task == 1) {
                        if (isset($call_outcome)) {
                            $event->callOutcome = $call_outcome['value'];
                            $event->callOutcomeId = $call_outcome['id'];
                        }
                    }
                }
            }

            $eventsForTeam[$key]->user_attendees = $user_attendees;
            $eventsForTeam[$key]->lead_attendees = $lead_attendees;
            $eventsForTeam[$key]->member_attendees = $member_attendees;
        }

        if ($client_id) {
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client = Client::whereId($client_id)->first();

            $is_default_team = $client->home_team_id === $current_team->id;

            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = Employee::whereClientId($client_id)->get();
            } else {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids = [];
                foreach ($team_users as $team_user) {
                    $user_ids[] = $team_user->user_id;
                }
                $users = Employee::whereIn('id', $user_ids)
                    ->get();
            }
        }


        return Inertia::render('Calendar/Show', [
            'calendar_events' => $eventsForTeam,
            'calendar_event_types' => CalendarEventType::whereClientId($client_id)->get(),
            'client_id' => $client_id,
            'client_users' => $users,
            'lead_users' => Lead::select('id', 'first_name', 'last_name')->get(),
            'member_users' => Member::select('id', 'first_name', 'last_name')->get(),
            'locations' => $locations,
        ]);
    }

    public function myCalendar(Request $request)
    {
        $client_id = request()->user()->client_id;

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        $locations = Location::all();

        $request->request->add(['viewUser' => $request->user()->id]);

        if ($request->get('start')) {
            $eventsForTeam = CalendarEvent::whereClientId($client_id)
                ->with('type', 'attendees', 'files')
                ->filter($request->only('search', 'start', 'end', 'viewUser'))
                ->get();
        } else {
            $eventsForTeam = [];
        }

        foreach ($eventsForTeam as $key => $event) {
            $user_attendees = [];
            $lead_attendees = [];
            $member_attendees = [];
            if ($event->attendees) {
                foreach ($event->attendees as $attendee) {
                    if ($attendee->entity_type == Employee::class) {
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
        }

        if ($client_id) {
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client = Client::whereId($client_id)->first();

            $is_default_team = $client->home_team_id === $current_team->id;

            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = Employee::whereClientId($client_id)->get();
            } else {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids = [];
                foreach ($team_users as $team_user) {
                    $user_ids[] = $team_user->user_id;
                }
                $users = Employee::whereIn('id', $user_ids)
                    ->get();
            }
        }


        return Inertia::render('Calendar/myCalendar', [
            'calendar_events' => $eventsForTeam,
            'calendar_event_types' => CalendarEventType::whereClientId($client_id)->get(),
            'client_id' => $client_id,
            'client_users' => $users,
            'lead_users' => Lead::select('id', 'first_name', 'last_name')->get(),
            'member_users' => Member::select('id', 'first_name', 'last_name')->get(),
            'locations' => $locations,
        ]);
    }

    public function quickView(Request $request)
    {
        $client_id = request()->user()->client_id;
        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }
        $locations = Location::all();
        $eventsByLocation = [];

        if (! $request->has('start')) {
            $request->merge(['start' => date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format('Y-m-d 00:00:00')
            )->getTimestamp())]);
            $request->merge(['end' => date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format('Y-m-d 23:59:59')
            )->getTimestamp())]);
        } else {
            $date = date('Y-m-d', strtotime($request->get('start')));
            $request->merge(['start' => date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format($date.' 00:00:00')
            )->getTimestamp())]);
            $request->merge(['end' => date('Y-m-d H:i:s', DateTime::createFromFormat(
                'Y-m-d H:i:s',
                (new DateTime())->format($date.' 23:59:59')
            )->getTimestamp())]);
        }

        foreach ($locations as $key => $location) {
            $eventsForTeam = CalendarEvent::whereClientId($client_id)
                    ->whereLocationId($location->id)
                    ->with('type', 'attendees', 'files')
                    ->filter($request->only('search', 'start', 'end', ))
                    ->get();
            $eventsByLocation[$key]['location_name'] = $location->name;
            $eventsByLocation[$key]['location_id'] = $location->id;
            $eventsByLocation[$key]['events'] = $eventsForTeam;
        }

        return Inertia::render('Calendar/QuickView', [
            'calendar_events_by_locations' => $eventsByLocation,
        ]);
    }

    public function eventTypes(Request $request)
    {
        return Inertia::render('Calendar/EventTypes/Show');
    }

    public function createEventType(Request $request)
    {
        return Inertia::render('Calendar/EventTypes/Create', [
        ]);
    }

    public function editEventType(Request $request, CalendarEventType $calendarEventType)
    {
        return Inertia::render('Calendar/EventTypes/Edit', [
            'calendarEventType' => $calendarEventType,
        ]);
    }
}
