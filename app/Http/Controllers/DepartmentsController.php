<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Departments\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;

class DepartmentsController extends Controller
{
    /** @var array<string, array<string>> */
    protected $rules = [
        'name' => ['string', 'required'],
        'id' => ['integer', 'sometimes', 'nullable'],
        'ability_ids' => ['array', 'sometimes'],
        'ability_ids.*' => ['array', 'sometimes'],
    ];

    public function index(Request $request): InertiaResponse|RedirectResponse
    {
        $user = $request->user();
        if ($user->client_id === null) {
            return Redirect::route('dashboard');
        }

        if ($request->user()->cannot('departments.read', Department::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Departments/Show', [
            'positions' => Position::all(),
            'filters' => $request->all('search', 'trashed', 'state'),
        ]);
    }

    public function create(Request $request): InertiaResponse|RedirectResponse
    {
        $user = $request->user();
        if ($user->client_id === null) {
            return Redirect::route('dashboard');
        }

        if ($user->cannot('departments.create', Department::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Departments/Create', [
            'positions' => Position::all(),
        ]);
    }

    public function edit(Department $department): InertiaResponse|RedirectResponse
    {
        if (request()->user()->cannot('departments.update', Department::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $department = Department::whereId($department->id)->with('positions')->first();

        return Inertia::render('Departments/Edit', [
            'department' => $department,
            'positions' => Position::all(),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.

    /**
     *
     * @return Collection<Department>
     */
    public function export(Request $request): Collection
    {
        $user = $request->user();
        if (($client_id = $user->client_id) === null) {
            abort(403);
        }

        if ($user->cannot('departments.read', Department::class)) {
            abort(403);
        }

        return Department::whereClientId($client_id)->get();
    }
}
