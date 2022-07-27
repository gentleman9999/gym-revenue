<?php

namespace App\Domain\Users\Actions;

use App\Domain\Teams\Models\Team;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function redirect;

/**
 * Switches teams on the session
 */
class SwitchTeam
{
    use AsAction;

    public function handle(Team $team)
    {
        session([
            'current_team' => [
                'id' => $team->id,
                'name' => $team->name,
                'client_id' => $team->client_id,
            ],
        ]);
        session(['client_id' => $team->client_id]);

        return $team;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return count($current_user->teams) > 1;
    }

    public function asController(ActionRequest $request, Team $team)
    {
        return $this->handle(
            $team
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Switched to '{$team->name}'")->flash();

        return redirect(config('fortify.home'), 303);
    }
}
