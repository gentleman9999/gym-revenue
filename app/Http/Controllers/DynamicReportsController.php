<?php

namespace App\Http\Controllers;

use App\Models\DynamicReport;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class DynamicReportsController extends Controller
{
    public function index()
    {
        return Inertia::render('DynamicReports/ReportsDashboard', [
            'report' => [],
        ]);
    }

    public function show()
    {
        return Inertia::render('DynamicReports/Show', [
            'reports' => DynamicReport::paginate(10)
                ->appends(request()->except('page')),
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('dynamic-reports.create', DynamicReport::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('DynamicReports/Create', [
            'reports' => DynamicReport::all(),
        ]);
    }

    public function edit(DynamicReport $dynamicReport)
    {
        if (request()->user()->cannot('dynamic-reports.update', DynamicReport::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $report = DynamicReport::whereId($dynamicReport->id)->first();

        return Inertia::render('DynamicReports/Edit', [
            'report' => $report,
        ]);
    }
}
