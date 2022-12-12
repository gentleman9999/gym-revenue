<?php

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateTeam
{
    use AsAction;

    public function handle(Team $team, array $payload): Team
    {
        TeamAggregate::retrieve($team->id)->update($payload)->persist();

        return $team->refresh();
    }

    public function __invoke($_, array $args): Team
    {
        $team = Team::find($args['id']);

        return $this->handle($team, $args);
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(ActionRequest $request): array
    {
        return [
            'name' => ['bail', 'required', 'max:50', Rule::unique('teams')->where('client_id', $request->client_id)],
//            'home_team' => ['sometimes', 'boolean'],
            'locations' => ['sometimes', 'array'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
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
            $team,
            $data
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team '{$team->name}' was updated")->flash();

//        return Redirect::back();
        return Redirect::route('teams.edit', $team->id);
    }
}
