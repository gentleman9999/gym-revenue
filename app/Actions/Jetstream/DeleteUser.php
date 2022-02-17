<?php

namespace App\Actions\Jetstream;

use App\Actions\Fortify\PasswordValidationRules;
use App\Aggregates\CapeAndBay\CapeAndBayUserAggregate;
use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function dd;

class DeleteUser implements DeletesUsers
{
    use PasswordValidationRules, AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($data, $current_user)
    {
        $client_id = $current_user->currentClientId();

        UserAggregate::retrieve($data['id'])->deleteUser($current_user->id ?? "Auto Generated", $data)->persist();
        if ($client_id) {
            ClientAggregate::retrieve($client_id)->deleteUser($current_user->id || "Auto Generated", $data)->persist();
        } else {
            //CapeAndBay User
            CapeAndBayUserAggregate::retrieve($data['team_id'])->deleteUser($current_user->id ?? "Auto Generated", $data)->persist();
        };
    }

    public function asController(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userData = $user->toArray();
        $userData['team_id'] = $request->user()->current_team_id;

        $current_team = $user->currentTeam()->first();
        $team_users = collect($current_team->team_users()->get());
        $acc_owners = [];

        foreach ($team_users as $team_user) {
            if($team_user->role === 'Account Owner') $acc_owners[] = $team_user->id;
        }
        if(count($team_users) < 1 ) {
            Alert::error("User '{$user->name}' cannot be deleted. Too few users found.")->flash();
            return Redirect::back();
        }

        if(count($acc_owners) < 1 ) {
            Alert::error("User '{$user->name}' cannot be deleted. At least one Account Owner is required for a team.")->flash();
            return Redirect::back();
        }

        $this->handle(
            $userData,
            $request->user(),
        );

        Alert::success("User '{$user->name}' was deleted")->flash();

//        return Redirect::route('users');
        return Redirect::back();
    }


    /**
     * Delete the given user.
     *
     * @param mixed $user
     * @return void
     */
    public function delete($user)
    {
        $this->run($user->id);
    }
}
