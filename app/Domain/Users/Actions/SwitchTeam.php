<?php

namespace App\Domain\Users\Actions;

use function abort;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function redirect;

class SwitchTeam
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
            'team_id' => ['required', 'exists:teams,id'],
        ];
    }

    public function handle($team_id, User $current_user)
    {
        $team = null;
        if ($current_user->client_id) {
            $team = Team::findOrFail($team_id)[0];
        } else {
            //capeandbay user
            $team = Team::withoutGlobalScopes()->findOrFail($team_id)[0];
        }
        $success = $current_user->switchTeam($team);

        if ($success) {
            return $team;
        } else {
            abort(403);
        }
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return count($current_user->teams) > 1;
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
            $request->user(),
        );
    }

    public function htmlResponse(Team $team): RedirectResponse
    {
        Alert::success("Switched to '{$team->name}'")->flash();

        return redirect(config('fortify.home'), 303);
    }
}
