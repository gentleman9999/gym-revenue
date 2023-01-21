<?php

namespace App\Http\Controllers;

use App\Domain\Notes\Model\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class NotesController extends Controller
{
    protected $rules = [
        'title' => ['string', 'required'],
        'id' => ['integer', 'sometimes', 'nullable'],
        'entity_id' => ['string', 'sometimes', 'nullable'],
        'entity_type' => ['string', 'sometimes', 'nullable'],
        'note' => ['string', 'sometimes', 'nullable'],
        'created_by_user_id' => ['string', 'sometimes', 'nullable'],
        'active' => ['integer', 'sometimes', 'nullable'],
        'created_at' => ['timestamp', 'sometimes', 'nullable'],
        'updated_at' => ['timestamp', 'sometimes', 'nullable'],
        'deleted_at' => ['timestamp', 'sometimes', 'nullable'],
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        $page_count = 10;
        $notes = Note::whereCreatedByUserId($client_id)
            ->filter($request->only('search', 'trashed'))
            ->paginate($page_count);

        return Inertia::render('Notes/Show', [
            'notes' => $notes,
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        if (request()->user()->cannot('notes.create', Note::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Notes/Create', [
            'created_by_user_id' => $client_id,
            //'title' => $title,
            //'note' => $note,
        ]);
    }

    public function edit($id)
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (! $id) {
            Alert::error("No Note ID provided")->flash();

            return Redirect::back();
        }
        if (request()->user()->cannot('notes.update', Note::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $note = Note::findOrFail($id)->toArray();

        return Inertia::render('Notes/Edit', [
            'note' => $note,
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $user = request()->user();

        if (is_null($user->client_id) || $user->cannot('notes.read', Note::class)) {
            abort(403);
        }

        return Note::whereCreatedByUserId($user->client_id)->get();
    }
}
