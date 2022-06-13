<?php

namespace App\Domain\Teams\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateTeam
{
    use AsAction;

    public function handle(string $id, array $payload): Team
    {
        TeamAggregate::retrieve($id)->update($payload)->persist();
        $team = Team::findOrFail($id);

        $client_id = $team->client->id ?? null;

        if ($client_id) {
            ClientAggregate::retrieve($client_id)->updateTeam($payload)->persist();
        }

        return $team;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'nullable', 'string', 'max:255', 'exists:clients,id'],
            'name' => ['required', 'max:50'],
//            'home_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, Team $team): Team
    {
        $data = $request->validated();

        return $this->handle(
            $team->id,
            $data
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team '{$team->name}' was updated")->flash();

        return Redirect::back();
    }
}
