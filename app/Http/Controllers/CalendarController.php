<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
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


        return Inertia::render('Calendar/Show', [
            'calendar_events' => $eventsForTeam,
            'calendar_event_types' => CalendarEventType::whereClientId($client_id)->get(),
            'client_id' => $client_id
        ]);
    }

}
