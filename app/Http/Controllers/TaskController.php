<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (!$client_id) {
            return Redirect::route('dashboard');
        }

        $typeTaskForClient = CalendarEventType::whereClientId($client_id)
            ->whereType('Task')
            ->first();
        $tasks = CalendarEvent::whereEventTypeId($typeTaskForClient->id)
            ->paginate(10);

        return Inertia::render('Task/Show', [
            'tasks' => $tasks,
            'filters' => $request->all('search', 'trashed', 'state')
        ]);
    }
}
