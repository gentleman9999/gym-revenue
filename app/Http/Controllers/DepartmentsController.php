<?php

namespace App\Http\Controllers;

use App\Domain\Departments\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class DepartmentsController extends Controller
{
    protected $rules = [
        'name' => ['string', 'required'],
        'id' => ['integer', 'sometimes', 'nullable'],
        'ability_ids' => ['array', 'sometimes'],
        'ability_ids.*' => ['array', 'sometimes'],
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('departments.read', Department::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $depts = Department::whereClientId($client_id)
            ->with('positions')
            ->filter($request->only('search', 'trashed'))
            ->sort()
            ->paginate(10)
            ->appends(request()->except('page'));

        return Inertia::render('Departments/Show', [
            'departments' => $depts,
            'positions' => Position::all(),
            'filters' => $request->all('search', 'trashed', 'state'),
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('departments.create', Department::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Departments/Create', [
        ]);
    }

    public function edit(Department $department)
    {
        if (request()->user()->cannot('departments.update', Department::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $department = Department::findOrFail($department->id)->with('positions')->first();

        return Inertia::render('Departments/Edit', [
            'department' => $department,
            'positions' => Position::all(),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = $request->user()->client_id;
        if (! $client_id) {
            abort(403);
        }
        if (request()->user()->cannot('departments.read', Department::class)) {
            abort(403);
        }

        $departments = Department::whereClientId($client_id)->get();

        return $departments;
    }
}
