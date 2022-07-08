<?php

namespace App\Http\Controllers;

use App\Domain\Reminders\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class RemindersController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $user_id = request()->user()->id;

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }
        $page_count = 10;
        $roles = request()->user()->getRoles();
        $security_group = request()->user()->securityGroup();

        $reminders = Reminder::with('client')
                ->whereUserId($user_id);

        return Inertia::render('Reminders/Show', [
            'reminders' => $reminders->paginate($page_count),
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->currentClientId();
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        if (request()->user()->cannot('reminders.create', Reminder::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Reminders/Create', [
            'user_id' => $client_id,
        ]);
    }

    public function edit($id)
    {
        if (! $id) {
            Alert::error("No Reminder ID provided")->flash();

            return Redirect::route('reminders');
        }

        return Inertia::render('Reminders/Edit', [
            'reminder' => Reminder::find($id),
        ]);
    }
}
