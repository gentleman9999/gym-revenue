<?php

declare(strict_types=1);

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
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
        ]);
    }

    public function handle(string $team_id, string $user_id): void
    {
        if (DB::table('team_user')->where(['team_id' => $team_id, 'user_id' => $user_id])->exists()) {
            throw ValidationException::withMessages(['email' => 'This user already belongs to the team']);
        }

        TeamAggregate::retrieve($team_id)->addMember($user_id)->persist();
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
        return $request->user()->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, Team $team, $teamMemberId): User
    {
        $this->team = $team;
        $user       = User::findOrFail($teamMemberId);
        $this->handle($team->id, $user->id);

        return $user;
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        Alert::success("Team Member'{$user->name}' added")->flash();

        return Redirect::route('teams.edit', $this->team->id);
    }

    /**
     * Add a new team member to the given team.
     *
     */
    public function add(mixed $user, mixed $team, string $email, ?string $role = null): void
    {
        $this->handle($team->id, User::whereEmail($email)->firstOrFail()->id);
    }
}
