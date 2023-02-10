<?php

declare(strict_types=1);

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Features;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class AddOrInviteTeamMembers
{
    use AsAction;

    private Team $team;
    private string $operation;

    /**
     * Get the validation rules for adding a team member.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email'],
        ];
    }

    public function handle(Team $team, array $emails): array
    {
        $this->team      = $team;
        $this->operation = Features::sendsTeamInvitations() ? "invited" : "added";

        $operation = $this->operation;

        $users_added_or_invited = [];
        if ($operation === 'added') {
            $users                  = User::whereIn('email', $emails)->get();
            $users_added_or_invited = AddTeamMembers::run($team, $users);
        } elseif ($operation === 'invited') {
            $users_added_or_invited = InviteTeamMembers::run($team, $emails);
        }

        $team->refresh();

        return $users_added_or_invited;
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
            Alert::success("Team Member'{$users[0]['name']}' $this->operation")->flash();
        } else {
            Alert::success("$num_users_added team members $this->operation")->flash();
        }

        return Redirect::route('teams.edit', $this->team->id);
    }
}
