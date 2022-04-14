<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarAttendee;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index($id)
    {
        //decrypt ID when we decide to obfuscate it in the future
        return Inertia::render('Invite/Show', [
            'attendeeData' => CalendarAttendee::whereId($id)->with('event')->first(),
        ]);
    }
}
