<?php

namespace App\Actions\Teams;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\TeamAggregate;
use App\Models\Team;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateTeam
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],
            'name' => ['required', 'max:50'],
//            'home_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }

    public function handle(Team $team, array $payload)
    {
        TeamAggregate::retrieve($team->id)->update($payload)->persist();
        $team = Team::findOrFail($id);

        $client_id = $team->client->id;

        //TODO:this could be changed to an AttachTeamtoClient event, and fire it off here
        if ($client_id) {
            ClientAggregate::retrieve($client_id)->updateTeam($payload)->persist();
        }

        return $team;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, Team $team)
    {
        $data = $request->validated();

        $team = $this->handle(
            $team,
            $data
        );

        Alert::success("Team '{$team->name}' was updated")->flash();

        return Redirect::back();
    }
}
