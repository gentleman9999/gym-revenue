<?php

namespace App\Domain\Teams\Actions;

use App\Domain\Teams\Models\Team;
use App\Domain\Teams\TeamAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreTeam
{
    use AsAction;

    public function handle(string $id)
    {
        TeamAggregate::retrieve($id)->restore()->persist();

        return Team::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('teams.restore', Team::class);
    }

    public function asController(Request $request, Team $team)
    {
        return $this->handle(
            $team->id,
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Team '{$team->name}' was restored")->flash();

        return Redirect::route('teams');
    }
}
