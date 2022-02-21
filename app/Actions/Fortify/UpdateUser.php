<?php

namespace App\Actions\Fortify;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
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
            'id' => ['required', 'integer', 'exists:users,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.request()->id],
            'altEmail' => ['sometimes','required', 'email'],
            'address1' => ['required'],
            'address2' => ['sometimes', 'nullable'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'jobTitle' => ['required'],
            'client_id' => ['sometimes','string', 'max:255', 'exists:clients,id'],
            'team_id' => ['required','integer', 'exists:teams,id'],
            'security_role' => ['nullable','string', 'max:255', 'exists:security_roles,id'],
//        'security_role' => ['required_with,client_id', 'exists:security_roles,id']
//            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['sometimes', 'digits:10'] //should be required, but seeders don't have phones.
        ];
    }

    public function handle($data, $current_user)
    {
        $client_id = $current_user->currentClientId();

        if(array_key_exists('password', $data)){
            $data['password'] = bcrypt($data['password']);
        }

        UserAggregate::retrieve($data['id'])->updateUser($current_user->id ?? "Auto Generated", $data)->persist();
        if ($client_id) {
            ClientAggregate::retrieve($client_id)->updateUser($current_user->id, $data)->persist();
        }
        return User::find($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('users.update', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {
        $user = $this->handle(
            $request->validated(),
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
        if(!array_key_exists('id', $input))
        {
            $input['id'] = $user->id;
        }
        $this->run($input, $user);
    }
}
