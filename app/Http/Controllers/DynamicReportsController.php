<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DynamicReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;

class DynamicReportsController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('DynamicReports/ReportsDashboard', [
            'report' => [],
        ]);
    }

    public function show(): InertiaResponse
    {
        return Inertia::render('DynamicReports/Show', [
            'reports' => DynamicReport::paginate(10)->appends(request()->except('page')),
        ]);
    }

    public function create(): InertiaResponse|RedirectResponse
    {
        $client_id = ($user = request()->user())->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }
        if ($user->cannot('dynamic-reports.create', DynamicReport::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('DynamicReports/Create', [
            'reports' => DynamicReport::all(),
        ]);
    }

    public function edit(DynamicReport $dynamic_report): InertiaResponse|RedirectResponse
    {
        if (request()->user()->cannot('dynamic-reports.update', DynamicReport::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $report = DynamicReport::whereId($dynamic_report->id)->first();

        return Inertia::render('DynamicReports/Edit', [
            'report' => $report,
        ]);
    }
}
