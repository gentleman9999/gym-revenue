<?php

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashTeam
{
    use AsAction;

    public function handle(Team $team)
    {
        TeamAggregate::retrieve($team->id)->trash()->persist();

        return Team::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.trash', Team::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, $id): Team
    {
        return $this->handle(
            $id,
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team '{$team->name}' was sent to the trash.")->flash();

        return Redirect::route('teams');
    }
}
