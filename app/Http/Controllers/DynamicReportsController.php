<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DynamicReportsController extends Controller
{
    public function index()
    {
        return Inertia::render('DynamicReports/ReportsDashboard', [
            'report' => [],
        ]);
    }
}
