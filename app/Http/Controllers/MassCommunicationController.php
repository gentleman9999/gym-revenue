<?php

namespace App\Http\Controllers;

use App\Domain\Teams\Models\Team;
use Inertia\Inertia;

class MassCommunicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $team = Team::find($session_team['id']);
        } else {
            $team = Team::find($user->default_team_id);
        }
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
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $team = Team::find($session_team['id']);
        } else {
            $team = Team::find($user->default_team_id);
        }
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
