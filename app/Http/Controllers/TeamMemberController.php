<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Jetstream\Actions\UpdateTeamMemberRole;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Contracts\InvitesTeamMembers;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;

class TeamMemberController extends Controller
{
    /**
     * Add a new team member to a team.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        $emails = $request->emails;

        if (Features::sendsTeamInvitations()) {
            foreach ($emails as $email) {
                app(InvitesTeamMembers::class)->invite(
                    $request->user(),
                    $team,
                    $email ?: '',
                    $request->role
                );
            }
        } else {
            foreach ($emails as $email) {
                $email_user = User::whereEmail($email)->first();

                if (! is_null($email_user)) {
                    app(AddsTeamMembers::class)->add(
                        $request->user(),
                        $team,
                        $email ?: '',
                        $email_user->role(),
                    );
                }
            }
        }

        return back(303);
    }

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

    /**
     * Remove the given user from the given team.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $teamId
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId, $userId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(RemovesTeamMembers::class)->remove(
            $request->user(),
            $team,
            $user = Jetstream::findUserByIdOrFail($userId)
        );

        if ($request->user()->id === $user->id) {
            return redirect(config('fortify.home'));
        }

        return back(303);
    }
}
