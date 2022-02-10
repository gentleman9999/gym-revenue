<?php

namespace App\Actions\Fortify;

use App\Aggregates\Clients\ClientAggregate;
use App\Helpers\Uuid;
use App\Models\Clients\Security\SecurityRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Events\TeamMemberAdded;
use Prologue\Alerts\Facades\Alert;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Actions\Jetstream\AddTeamMember;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\Database\Role;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUser implements UpdatesUserProfileInformation
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.request()->id],
            'client_id' => ['sometimes','string', 'max:255', 'exists:clients,id'],
            'team' => ['required','integer', 'exists:teams,id'],
            'security_role' => ['nullable','string', 'max:255', 'exists:security_roles,id'],
//        'security_role' => ['required_with,client_id', 'exists:security_roles,id']
//            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ];
    }

    public function handle($data, $current_user)
    {
        $client_id = $current_user->currentClientId();

        if(array_key_exists('password', $data)){
            $data['password'] = bcrypt($data['password']);
        }

        if ($client_id) {
            ClientAggregate::retrieve($client_id)->updateUser($current_user->id || "Auto Generated", $data)->persist();
        } else {
            //CapeAndBay User
            dd('not yet implemented', $data);
        }
        return User::find($data['id']);
    }

    public function asController(Request $request)
    {
        $user = $this->handle(
            $request->all(),
            $request->user(),
        );

        Alert::success("User '{$user->name}' was updated")->flash();

//        return Redirect::route('users');
        return Redirect::back();
    }


    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        $this->run($input);
    }
}
