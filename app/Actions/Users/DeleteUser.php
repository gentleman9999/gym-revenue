<?php

namespace App\Actions\Users;

use App\Aggregates\CapeAndBay\CapeAndBayUserAggregate;
use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Enums\SecurityGroupEnum;
use App\Models\User;
use function collect;
use Illuminate\Support\Facades\Redirect;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use function request;

class DeleteUser implements DeletesUsers
{
    use PasswordValidationRules;
    use AsAction;

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

    public function handle(User|int $user)
    {
        $user_id = $user->id ?? $user;

        UserAggregate::retrieve($user_id)->deleteUser()->persist();
        if ($user->client_id) {
            ClientAggregate::retrieve($client_id)->deleteUser()->persist();
        } else {
            //CapeAndBay User
            CapeAndBayUserAggregate::retrieve($data['team_id'])->deleteUser($$data)->persist();
        };
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.delete', User::class);
    }

    public function asController(ActionRequest $request, User $user)
    {
        $me = request()->user();
        $userData = $user->toArray();
        $userData['team_id'] = $request->user()->current_team_id;

        $current_team = $me->currentTeam()->first();
        $team_users = collect($current_team->team_users()->with('user')->get());
        $non_admins = [];

        foreach ($team_users as $team_user) {
            if (! $team_user->user->inSecurityGroup(SecurityGroupEnum::ADMIN)) {
                $non_admins[] = $team_user->id;
            }
        }
//
//        if (count($non_admins) < 1) {
//            Alert::error("User '{$user->name}' cannot be deleted. Too few users found on team.")->flash();
//
//            return Redirect::back();
//        }

        $this->handle(
            $user,
//            $userData,
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
