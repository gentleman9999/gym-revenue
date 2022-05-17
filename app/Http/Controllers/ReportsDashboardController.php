<?php

namespace App\Http\Controllers;

use Bouncer;
use Inertia\Inertia;

class ReportsDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = $user->currentTeam;
        $client_detail = $team->client_details()->first();
        $team_name = $team->name;

        if (!is_null($client_detail)) {
//            $widgets = $this->service->getDashboardWidgets();

            return Inertia::render('Reports/ReportsDashboard', [
                    'teamName' => $team_name,
//                'widgets' => $widgets,
                ]
            );
        } else {
            //flash error, redirect somewhere
            abort(403);
        }
    }

    public function page($type)
    {
        $user = auth()->user();
        $team = $user->currentTeam;
        $client_detail = $team->client_details()->first();
        $team_name = $team->name;

        if (!$type) {
            abort(404);
        }

        if (!is_null($client_detail)) {
//            $widgets = $this->service->getDashboardWidgets();

            return Inertia::render("Reports/{$type}/{$type}Page", [
                    'teamName' => $team_name,
//                'widgets' => $widgets,
                ]
            );
        } else {
            //flash error, redirect somewhere
            abort(403);
        }
    }
}
