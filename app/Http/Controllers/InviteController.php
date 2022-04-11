<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Clients\Client;
use App\Models\Endusers\Lead;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class InviteController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        return Inertia::render('Invite/Show', [
        ]);
    }
}
