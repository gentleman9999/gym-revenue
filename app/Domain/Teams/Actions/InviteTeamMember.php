<?php

namespace App\Domain\Teams\Actions;

use function __;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\InvitesTeamMembers;
use Laravel\Jetstream\Events\InvitingTeamMember;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class InviteTeamMember implements InvitesTeamMembers
{
    use AsAction;

    public function handle(Team $team, string $email): void
    {
        $this->validate($team, $email);

        TeamAggregate::retrieve($team->id)->inviteMember($email)->persist();

//        InvitingTeamMember::dispatch($team, $email, $role);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
//        Gate::forUser($user)->authorize('addTeamMember', $team);
        return $current_user->can('teams.update', Team::class);
    }

    /**
     * Invite a new team member to the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    public function invite($user, $team, string $email, string $role = null)
    {
        $this->handle($team, $email);
    }

    /**
     * Validate the invite member operation.
     *
     * @param  mixed  $team
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    protected function validate($team, string $email, ?string $role)
    {
        Validator::make([
            'email' => $email,
//            'role' => $role,
        ], $this->rules($team), [
            'email.unique' => __('This user has already been invited to the team.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnTeam($team, $email)
        )->validateWithBag('addTeamMember');
    }

    /**
     * Get the validation rules for inviting a team member.
     *
     * @param  mixed  $team
     * @return array
     */
    protected function rules($team)
    {
        return array_filter([
            'email' => ['required', 'email', Rule::unique('team_invitations')->where(function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })],
//            'role' => Jetstream::hasRoles()
//                            ? ['required', 'string', new Role()]
//                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the team.
     *
     * @param  mixed  $team
     * @param  string  $email
     * @return \Closure
     */
    protected function ensureUserIsNotAlreadyOnTeam($team, string $email)
    {
        return function ($validator) use ($team, $email) {
            $validator->errors()->addIf(
                $team->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the team.')
            );
        };
    }
}
