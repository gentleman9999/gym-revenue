<?php

namespace App\Domain\Teams\Actions;

use function __;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class AddTeamMember implements AddsTeamMembers
{
    use AsAction;

    private Team $team;

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email'],
//            'email' => ['required', 'email', 'exists:users'],
//            'role' => Jetstream::hasRoles()
//                            ? ['required', 'string', new Role()]
//                            : null,
        ]);
    }

    public function handle(Team $team, User $user): User
    {
        $this->team = $team;
//        Gate::forUser($user)->authorize('addTeamMember', $team);

        $this->validate($team, $user->email);

        //TODO:should be adding by id, not email (should be INVITING by email not id)
        TeamAggregate::retrieve($team->id)->addMember($user->email)->persist();

//        TeamMemberAdded::dispatch($team, $newTeamMember);
        $team->refresh();

        return $user->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
//        Gate::forUser($user)->authorize('addTeamMember', $team);

        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, Team $team, $teamMemberId): User
    {
        $teamMember = User::findOrFail($teamMemberId);
//        return $this->handle(
//            $team,
//            $teamMember,
//        );
        return  $this->handle(
            $team,
            $teamMember,
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("Team Member'{$user->name}' added")->flash();

        return Redirect::route('teams.edit', $this->team->id);
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
        $this->handle($team, User::whereEmail($email)->firstOrFail());
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
