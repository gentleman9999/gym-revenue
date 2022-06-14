<?php

namespace App\Domain\Teams\Actions;

use function __;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Events\TeamMemberRemoved;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RemoveTeamMember implements RemovesTeamMembers
{
    use AsAction;

    public function handle(Team $team, string $email)
    {
//        Gate::forUser($user)->authorize('addTeamMember', $team);
//        $this->ensureUserDoesNotOwnTeam($teamMember, $team);

        TeamAggregate::retrieve($team->id)->removeMember($email)->persist();

//        TeamMemberRemoved::dispatch($team, $teamMember);
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

    /**
     * Remove the team member from the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  mixed  $teamMember
     * @return void
     */
    public function remove($user, $team, $teamMember)
    {
        $this->handle();
    }

    /**
     * Ensure that the currently authenticated user does not own the team.
     *
     * @param  mixed  $teamMember
     * @param  mixed  $team
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
