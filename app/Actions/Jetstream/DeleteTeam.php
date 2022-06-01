<?php

namespace App\Actions\Jetstream;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\TeamAggregate;
use App\Models\Team;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteTeam implements DeletesTeams
{
    use AsAction;

    public function handle($id, $current_user = null)
    {
        $team = Team::findOrFail($id);

        TeamAggregate::retrieve($id)->delete($id, $current_user->id ?? 'Auto Generated')->persist();

        $client_id = $team->client->id;

        if ($client_id) {
            ClientAggregate::retrieve($client_id)->deleteTeam($id, $current_user->id ?? 'Auto Generated')->persist();
        }

        return $team;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.delete', Team::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $team = Jetstream::newTeamModel()->findOrFail($id);

        app(ValidateTeamDeletion::class)->validate(request()->user(), $team);

//        $deleter = app(DeletesTeams::class);

//        $deleter->delete($team);

        $team = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Team '{$team->name}' was deleted")->flash();

        return Redirect::back();
    }

    /**
     * Delete the given team.
     *
     * @param  mixed  $team
     * @return void
     */
    public function delete($team)
    {
//        $team->purge();
        return $this->handle($team->id);
    }
}
