<?php

declare(strict_types=1);

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\UpdatesTeamNames;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateTeamName implements UpdatesTeamNames
{
    use AsAction;

    public function handle(string $id, string $name): Team
    {
//        return UpdateTeam::run($id, ['name' => $name]);

        $team = Team::where('id', $id)->first();

        return UpdateTeam::run($team, ['name' => $name]);
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:50'],
        ];
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
        $current_user = $request->user();

        return $current_user->can('teams.update', Team::class);
    }

    public function asController(ActionRequest $request, Team $team): Team
    {
        $data = $request->validated();

        return $this->handle(
            $team->id,
            $data['name']
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team name '{$team->name}' was updated")->flash();

        return Redirect::back();
    }

    /**
     * Validate and update the given team's name.
     *
     * @param array<string, mixed> $input
     * @return void
     */
    public function update(mixed $user, mixed $team, array $input)
    {
//        Gate::forUser($user)->authorize('update', $team);

//        Validator::make($input, [
//            'name' => ['required', 'string', 'max:255'],
//        ])->validateWithBag('updateTeamName');
        return $this->handle($team->id, $input['name']);
    }
}
