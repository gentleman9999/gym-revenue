<?php

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
     * @param \Illuminate\Http\Request $request
     * @param int $teamId
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $teamId, $userId)
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
