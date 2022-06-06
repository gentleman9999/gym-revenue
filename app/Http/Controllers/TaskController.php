<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\Endusers\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        $typeTaskForClient = CalendarEventType::whereClientId($client_id)
            ->whereType('Task')
            ->first();
        $tasks = CalendarEvent::whereEventTypeId($typeTaskForClient->id)
            ->with('type')
            ->paginate(10);


        foreach ($tasks as $key => $event) {
            $tasks[$key]->event_owner = User::whereId($event['owner_id'])->first() ?? null;
        }

        return Inertia::render('Task/Show', [
            'tasks' => $tasks,
            'client_id' => $client_id,
            'client_users' => [],
            'lead_users' => Lead::whereClientId($client_id)->select('id', 'first_name', 'last_name')->get(),
            'calendar_event_types' => CalendarEventType::whereClientId($client_id)->get(),
            'filters' => $request->all('search', 'trashed', 'state'),
        ]);
    }
}
