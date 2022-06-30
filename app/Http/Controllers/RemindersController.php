<?php

namespace App\Http\Controllers;

use App\Enums\SecurityGroupEnum;
use App\Models\Reminder;
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
        $reminders = Reminder::with('client')
                ->whereUserId($user_id);

        return Inertia::render('Reminders/Show', [
            'reminders' => $reminders->paginate($page_count),
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

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        if (is_null($client_id)) {
            abort(403);
        }
        $roles = request()->user()->getRoles();
        $security_group = request()->user()->securityGroup();

        if ($security_group === SecurityGroupEnum::ADMIN || $security_group === SecurityGroupEnum::ACCOUNT_OWNER) {
            $reminders = Reminder::with('client')
                ->whereClientId($client_id)
                ->whereUserId(null)
                ->filter($request->only('search', 'trashed'))
                ->get();
        } else {
            $reminders = Reminder::with('client')
                ->whereClientId($client_id)
                ->whereUserId(null)
                ->where('permissions', 'like', '%'.strtolower(str_replace(' ', '_', $roles[0])).'%')
                ->filter($request->only('search', 'trashed'))
                ->get();
        }

        return $reminders;
    }
}
