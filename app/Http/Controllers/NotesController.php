<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;

class NotesController extends Controller
{
    /** @var array<string, array<string>>  */
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

    public function index(Request $request): InertiaResponse|RedirectResponse
    {
        $client_id = $request->user()->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        return Inertia::render('Notes/Show');
    }

    public function create(): InertiaResponse|RedirectResponse
    {
        $client_id = ($user = request()->user())->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        if ($user->cannot('notes.create', Note::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Notes/Create', [
            'created_by_user_id' => $client_id,
        ]);
    }

    public function edit(?string $id = null): InertiaResponse|RedirectResponse
    {
        $client_id = ($user = request()->user())->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        if ($id === null) {
            Alert::error("No Note ID provided")->flash();

            return Redirect::back();
        }

        if ($user->cannot('notes.update', Note::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Notes/Edit', ['note' => Note::findOrFail($id)]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.

    /**
     * @return Collection<Note>
     */
    public function export(): Collection
    {
        $user = request()->user();

        if ($user->client_id === null || $user->cannot('notes.read', Note::class)) {
            abort(403);
        }

        return Note::whereCreatedByUserId($user->client_id)->get();
    }
}
