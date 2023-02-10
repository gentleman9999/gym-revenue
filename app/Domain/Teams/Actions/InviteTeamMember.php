<?php

declare(strict_types=1);

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\InvitesTeamMembers;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

use function __;

class InviteTeamMember implements InvitesTeamMembers
{
    use AsAction;

    private Team $team;

    public function handle(Team $team, string $email): string
    {
        $this->validate($team, $email);

        TeamAggregate::retrieve($team->id)->inviteMember($email)->persist();

//        InvitingTeamMember::dispatch($team, $email, $role);

        return $email;
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
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
     */
    public function invite(mixed $user, mixed $team, string $email, ?string $role = null): void
    {
        $this->handle($team, $email);
    }

    /**
     * Get the validation rules for inviting a team member.
     *
     * @return array
     */
    public function rules(mixed $team): array
    {
        return array_filter([
            'email' => ['required', 'email', Rule::unique('team_invitations')->where(function ($query) use ($team): void {
                $query->where('team_id', $team->id);
            })],
//            'role' => Jetstream::hasRoles()
//                            ? ['required', 'string', new Role()]
//                            : null,
        ]);
    }

    /**
     * Validate the invite member operation.
     *
     * @param  string|null  $role
     */
    protected function validate(mixed $team, string $email): void
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
     * Ensure that the user is not already on the team.
     *
     */
    protected function ensureUserIsNotAlreadyOnTeam(mixed $team, string $email): \Closure
    {
        return function ($validator) use ($team, $email): void {
            $validator->errors()->addIf(
                $team->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the team.')
            );
        };
    }
}
