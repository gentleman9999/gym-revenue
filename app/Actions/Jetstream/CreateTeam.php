<?php

namespace App\Actions\Jetstream;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\TeamAggregate;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Events\AddingTeam;
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
            'user_id' => ['sometimes', 'exists:users,id'],
//            'personal_team' => ['sometimes', 'boolean'],
            'default_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
            'shouldCreateTeam' => ['sometimes', 'boolean'],
        ];
    }

    public function handle($data, $current_user = null)
    {
        if ($current_user) {
            AddingTeam::dispatch($current_user);
        }
//        $team = $current_user->ownedTeams()->create([
//            'name' => $data['name'],
//            'personal_team' => false,
//        ]);

        $id = (Team::max('id') ?? 0) + 1;
        $data['id'] = $id;

        TeamAggregate::retrieve($id)->createTeam($data)->persist();

        $client_id = $data['client_id'] ?? null;

        if ($client_id) {
            ClientAggregate::retrieve($data['client_id'])->createTeam($data)->persist();
        }

        $team = Team::findOrFail($id);
//        if($current_user){
//            $current_user->switchTeam($team);
//        }

        return $team;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.create', Team::class);
    }

    public function asController(ActionRequest $request)
    {
        $team = $this->handle(
            $request->validated(),
            $request->user(),
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
        $team = $this->handle($input, $user);

        return $team;
    }
}
