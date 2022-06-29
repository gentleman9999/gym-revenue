<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MassCommunicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = $user->currentTeam;
        $team_name = $team->name;

        if (! is_null($team->client)) {
            return Inertia::render(
                'MassCommunication/Show',
                [
                    'teamName' => $team_name,
                ]
            );
        } else {
            abort(403);
        }
    }

    public function page($type)
    {
        $user = auth()->user();
        $team = $user->currentTeam;
        $team_name = $team->name;

        if (! $type) {
            abort(404);
        }

        if (! is_null($team->client)) {
            return Inertia::render(
                "MassCommunication/{$type}/{$type}Page",
                [
                    'teamName' => $team_name,
                ]
            );
        } else {
            abort(403);
        }
    }
}
