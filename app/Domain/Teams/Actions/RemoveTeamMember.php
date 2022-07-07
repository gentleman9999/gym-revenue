<?php

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
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

    public function handle(Team $team, User $teamMember): array
    {
        TeamAggregate::retrieve($team->id)->removeMember($teamMember->id)->persist();

//        $team->removeUser($teamMember);
//
//        TeamMemberRemoved::dispatch($team, $teamMember);
        return ['team' => $team->refresh(), 'user' => $teamMember->refresh()];
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

    //TODO: use implicit route binding on teamMemberId
    public function asController(ActionRequest $request, Team $team, $teamMemberId): array
    {
        $teamMember = User::findOrFail($teamMemberId);

        return $this->handle(
            $team,
            $teamMember,
        );
    }

    public function htmlResponse(array $response): RedirectResponse
    {
        Alert::success("Team Member'{$response['user']->name}' was removed")->flash();

        return Redirect::route('teams.edit', $response['team']->id);
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
//    protected function ensureUserDoesNotOwnTeam($teamMember, $team)
//    {
//        if ($teamMember->id === $team->owner->id) {
//            throw ValidationException::withMessages([
//                'team' => [__('You may not leave a team that you created.')],
//            ])->errorBag('removeTeamMember');
//        }
//    }
}
