<?php

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class AddTeamMembers
{
    use AsAction;

    private Team $team;

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
            ];
    }

    public function handle(Team $team, Collection | array $users): array
    {
        $this->team = $team;

        $users_added = [];
        foreach ($users as $user) {
            $users_added[] = AddTeamMember::run($team, $user);
        }

        $team->refresh();

        return $users_added;
    }

    public function authorize(ActionRequest $request): bool
    {
//        Gate::forUser($user)->authorize('addTeamMember', $team);

        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, Team $team): array
    {
        return $this->handle(
            $team,
            $request->validated()['emails']
        );
    }

    public function htmlResponse(array $users): RedirectResponse
    {
        $num_users_added = count($users);
        if ($num_users_added === 1) {
            Alert::success("Team Member'{$users[0]->name}' added")->flash();
        } else {
            Alert::success("$num_users_added team members added")->flash();
        }

        return Redirect::route('teams.edit', $this->team->id);
    }
}
