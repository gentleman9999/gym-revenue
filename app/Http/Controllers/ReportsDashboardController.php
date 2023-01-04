<?php

namespace App\Http\Controllers;

use App\Support\CurrentInfoRetriever;
use Inertia\Inertia;

class ReportsDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = CurrentInfoRetriever::getCurrentTeam();
        $team_name = $team->name;

        if (! is_null($team->client)) {
//            $widgets = $this->service->getDashboardWidgets();

            return Inertia::render(
                'Reports/ReportsDashboard',
                [
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
        $team = CurrentInfoRetriever::getCurrentTeam();
        $team_name = $team->name;

        if (! $type) {
            abort(404);
        }

        if (! is_null($team->client)) {
//            $widgets = $this->service->getDashboardWidgets();

            return Inertia::render(
                "Reports/{$type}/{$type}Page",
                [
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
