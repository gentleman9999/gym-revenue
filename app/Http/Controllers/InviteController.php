<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarAttendee;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index($id)
    {
        $client_id = request()->user()->currentClientId();

        $attendeeData = CalendarAttendee::whereId($id)->with('event')->first();
        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        return Inertia::render('Invite/Show', [
            'attendeeData' => $attendeeData,
        ]);
    }
}
