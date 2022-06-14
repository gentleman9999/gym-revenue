<?php

namespace App\Domain\Teams\Actions;

use function __;
use App\Actions\Jetstream\User;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class AddTeamMember implements AddsTeamMembers
{
    use AsAction;

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    protected function rules()
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
//            'role' => Jetstream::hasRoles()
//                            ? ['required', 'string', new Role()]
//                            : null,
        ]);
    }

    public function handle(Team $team, string $email)
    {
//        Gate::forUser($user)->authorize('addTeamMember', $team);

        $this->validate($team, $email);

        TeamAggregate::retrieve($team->id)->addMember($email)->persist();

//        TeamMemberAdded::dispatch($team, $newTeamMember);
    }

    public function authorize(ActionRequest $request): bool
    {
//        Gate::forUser($user)->authorize('addTeamMember', $team);

        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    /**
     * Add a new team member to the given team.
     *
     * @param  mixed  $user
     * @param  mixed  $team
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
    public function add($user, $team, string $email, string $role = null)
    {
        $this->handle($team, $email);
    }

    /**
     * Validate the add member operation.
     *
     * @param  mixed  $team
     * @param  string  $email
     * @param  string|null  $role
     * @return void
     */
//    protected function validate($team, string $email, ?string $role)
    protected function validate($team, string $email)
    {
        Validator::make([
            'email' => $email,
//            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnTeam($team, $email)
        )->validateWithBag('addTeamMember');
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
