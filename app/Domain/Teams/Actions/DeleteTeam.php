<?php

declare(strict_types=1);

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

use function app;
use function request;

class DeleteTeam implements DeletesTeams
{
    use AsAction;

    public function handle(Team $team): Team
    {
        TeamAggregate::retrieve($team->id)->delete()->persist();

        return $team;
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

        return $current_user->can('teams.delete', Team::class);
    }

    public function asController(ActionRequest $request, Team $team): Team
    {
        app(ValidateTeamDeletion::class)->validate(request()->user(), $team);

//        $deleter = app(DeletesTeams::class);

//        $deleter->delete($team);
        return $this->handle(
            $team
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
     * @return void
     */
    public function delete(mixed $team): Team
    {
//        $team->purge();
        return $this->handle($team);
    }
}
