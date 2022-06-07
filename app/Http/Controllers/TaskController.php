<?php

namespace App\Http\Controllers;

use App\Models\Calendar\CalendarEvent;
use App\Models\Calendar\CalendarEventType;
use App\Models\User;
use DateTime;
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

        /** CHECKING THE PAGE REQUEST FOR START PARAMS
         * If param 'start' doesn't exist, we get the start and end of today.
         * If param 'start' exists, we get the start and end of that date
         */
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

        $typeTaskForClient = CalendarEventType::whereClientId($client_id)
            ->whereType('Task')
            ->first();


        $tasks = CalendarEvent::whereEventTypeId($typeTaskForClient->id)
            ->with('type')
            ->filter($request->only('search', 'start', 'end'))
            ->paginate(10);

        $incomplete_tasks = CalendarEvent::whereEventTypeId($typeTaskForClient->id)
            ->whereNull('event_completion')
            ->with('type')
            ->paginate(10);

        $completed_tasks = CalendarEvent::whereEventTypeId($typeTaskForClient->id)
            ->whereNotNull('event_completion')
            ->with('type')
            ->paginate(10);


        foreach ($tasks as $key => $event) {
            $tasks[$key]->event_owner = User::whereId($event['owner_id'])->first() ?? null;
        }

        return Inertia::render('Task/Show', [
            'tasks' => $tasks,
            'client_id' => $client_id,
            'client_users' => [],
            'lead_users' => [],
            'calendar_event_types' => CalendarEventType::whereClientId($client_id)->get(),
            'filters' => $request->all('search', 'trashed', 'state'),
            'incomplete_tasks' => $incomplete_tasks,
            'overdue_tasks' => CalendarEvent::whereEventTypeId($typeTaskForClient->id)
                ->whereNull('event_completion')
                ->whereDate('start', '<', date('Y-m-d H:i:s'))
                ->with('type')
                ->get(),
            'completed_tasks' => $completed_tasks,
        ]);
    }
}
