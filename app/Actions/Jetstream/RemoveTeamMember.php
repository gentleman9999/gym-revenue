<?php

namespace App\Actions\Jetstream;

use function __;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Events\TeamMemberRemoved;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RemoveTeamMember implements RemovesTeamMembers
{
    use AsAction;

    public function handle(Team $team, User $teamMember)
    {
        $team->removeUser($teamMember);

        TeamMemberRemoved::dispatch($team, $teamMember);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
//
//        if (! Gate::forUser($user)->check('removeTeamMember', $team) &&
//            $user->id !== $teamMember->id) {
//            throw new AuthorizationException();
//        }
    }

    public function asController(ActionRequest $request, Team $team, $teamMemberId)
    {
        $teamMember = User::findOrFail($teamMemberId);
        $this->handle(
            $team,
            $teamMember,
        );

        Alert::success("Team Member'{$teamMember->name}' was removed")->flash();

        return Redirect::route('teams.edit', $team->id);
    }

    /**
     * Remove the team member from the given team.
     *
     * @param mixed $user
     * @param mixed $team
     * @param mixed $teamMember
     * @return void
     */
    public function remove($user, $team, $teamMember)
    {
        $this->handle($team, $teamMember->email);
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     *
     * @param mixed $teamMember
     * @param mixed $team
     * @return void
     */
    protected function ensureUserDoesNotOwnTeam($teamMember, $team)
    {
        if ($teamMember->id === $team->owner->id) {
            throw ValidationException::withMessages([
                'team' => [__('You may not leave a team that you created.')],
            ])->errorBag('removeTeamMember');
        }
    }
}
