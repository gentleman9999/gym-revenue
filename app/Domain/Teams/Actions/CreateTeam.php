<?php

namespace App\Domain\Teams\Actions;

use App\Actions\GymRevAction;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class CreateTeam extends GymRevAction implements CreatesTeams
{
    public function handle(array $payload): Team
    {
        $id = Uuid::new();

        TeamAggregate::retrieve($id)->create($payload)->persist();

        return Team::findOrFail($id);
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(ActionRequest $request): array
    {
        return [
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],
            'name' => ['bail', 'required', 'max:50', Rule::unique('teams')->where('client_id', $request->client_id)],
            'home_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.create', Team::class);
    }

    public function asController(ActionRequest $request): Team
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team '{$team->name}' was created")->flash();

        return Redirect::route('teams.edit', $team->id);
    }

    public function create($user, array $input): Team
    {
        return $this->handle($input);
    }
}
