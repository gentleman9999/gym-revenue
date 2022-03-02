<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\CalendarAggregate;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        $eventsForTeam = CalendarEvent::whereClient_id($client_id)->get();

        return Inertia::render('Calendar/Show', [
            'events' => $eventsForTeam,
            'client_id' => $client_id
        ]);
    }


    public function update(Request $request, $id)
    {

        return Redirect::route('calendar');
    }

    public function store(Request $request)
    {

        return Redirect::route('calendar');
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
