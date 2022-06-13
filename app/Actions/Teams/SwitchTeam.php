<?php

namespace App\Actions\Teams;

use App\Domain\Teams\Models\Team;
use App\Models\User;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

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
            $team = \App\Domain\Teams\Models\Team::findOrFail($team_id)[0];
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
        $team = $this->handle(
            $request->validated(),
            $request->user(),
        );


        Alert::success("Switched to '{$team->name}'")->flash();

        return redirect(config('fortify.home'), 303);
    }
}
