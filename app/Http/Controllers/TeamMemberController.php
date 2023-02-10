<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Jetstream\Actions\UpdateTeamMemberRole;
use Laravel\Jetstream\Jetstream;

class TeamMemberController extends Controller
{
    /**
     * Update the given team member's role.
     *
     * @param int $team_id
     * @param string $user_id
     */
    public function update(Request $request, $teamId, $userId): \Illuminate\Http\RedirectResponse
    {
        app(UpdateTeamMemberRole::class)->update(
            $request->user(),
            Jetstream::newTeamModel()->findOrFail($teamId),
            $userId,
            $request->role
        );

        return back(303);
    }
}
