<?php

namespace App\Actions\Teams;

use App\Aggregates\TeamAggregate;
use App\Helpers\Uuid;
use App\Models\Team;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateTeam implements CreatesTeams
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
            'home_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }

    public function handle(array $payload)
    {
        $id = Uuid::new();
        $payload['id'] = $id;

        TeamAggregate::retrieve($id)->create($payload)->persist();

        return Team::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.create', Team::class);
    }

    public function asController(ActionRequest $request)
    {
        $team = $this->handle(
            $request->validated()
        );

        Alert::success("Team '{$team->name}' was created")->flash();

        return Redirect::route('teams.edit', $team->id);
    }

    /**
     * Validate and create a new team for the given user.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return mixed
     */
    public function create($user, array $input)
    {
        $team = $this->handle($input);

        return $team;
    }
}
