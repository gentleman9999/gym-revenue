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

        $eventsForTeam = CalendarEvent::whereClient_id($client_id)
            ->with('type')
            ->get();

        return Inertia::render('Calendar/Show', [
            'events' => $eventsForTeam,
            'client_id' => $client_id
        ]);
    }


    public function create(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        return Inertia::render('Calendar/Create', [
            'client_id' => $client_id
        ]);
    }

    public function store(Request $request)
    {

        return Redirect::route('calendar');
    }

    public function edit(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        return Inertia::render('Calendar/Edit', [
            'client_id' => $client_id
        ]);
    }


    public function trash(Request $request, $id)
    {

        return Redirect::route('calendar');
    }

    public function delete(Request $request, $id)
    {

        return Redirect::route('calendar');
    }

    public function restore(Request $request, $id)
    {

        return Redirect::back();
    }

}
