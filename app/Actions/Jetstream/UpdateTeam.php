<?php

namespace App\Actions\Jetstream;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\TeamAggregate;
use App\Models\Team;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateTeam implements DeletesTeams
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
//            'user_id' => ['sometimes', 'exists:users,id'],
//            'personal_team' => ['sometimes', 'boolean'],
//            'default_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        TeamAggregate::retrieve($data['id'])->update($data, $current_user->id ?? 'Auto Generated')->persist();
        $team = Team::findOrFail($data['id']);

        $client_id = $team->client->id;

        if ($client_id) {
            ClientAggregate::retrieve($client_id)->updateTeam($data, $current_user->id ?? 'Auto Generated')->persist();
        }

        return $team;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        $team = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Team '{$team->name}' was updated")->flash();

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
