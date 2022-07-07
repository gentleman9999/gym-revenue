<?php

namespace App\Domain\Teams\Actions;

use function app;
use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function request;

class DeleteTeam implements DeletesTeams
{
    use AsAction;

    public function handle(string $id): Team
    {
        $team = Team::findOrFail($id);

        TeamAggregate::retrieve($id)->delete()->persist();

        if ($team->client_id) {
            ClientAggregate::retrieve($team->client_id)->deleteTeam()->persist();
        }

        return $team;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.delete', Team::class);
    }

    public function asController(ActionRequest $request, Team $team): Team
    {
        app(ValidateTeamDeletion::class)->validate(request()->user(), $team);

//        $deleter = app(DeletesTeams::class);

//        $deleter->delete($team);
        return $this->handle(
            $team->id
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team '{$team->name}' was deleted")->flash();

        return Redirect::route('teams');
    }

    /**
     * Delete the given team.
     *
     * @param mixed $team
     * @return void
     */
    public function delete($team): Team
    {
//        $team->purge();
        return $this->handle($team->id);
    }
}
