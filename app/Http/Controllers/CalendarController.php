<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        if(is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        if($request->get('start')) {
            $eventsForTeam = CalendarEvent::whereClient_id($client_id)
                ->with('type')
                ->filter($request->only('search', 'start', 'end'))
                ->get();

        } else {
            $eventsForTeam = [];
        }

        foreach ($eventsForTeam as $key => $event)
        {
            $eventsForTeam[$key]->attendees = $event->attendees;
            $eventsForTeam[$key]->lead_attendees = $event->lead_attendees;
        }

        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            $is_default_team = $client->default_team_name->value == $current_team->id;

            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = User::whereHas('detail', function ($query) use ($client_id) {
                    return $query->whereName('associated_client')->whereValue($client_id);
                })->get();
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
            'lead_users' => Lead::whereClientId($client_id)->select('id', 'first_name', 'last_name')->get()
        ]);
    }

}
