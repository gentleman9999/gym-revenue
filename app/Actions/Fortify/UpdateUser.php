<?php

namespace App\Actions\Fortify;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateUser implements UpdatesUserProfileInformation
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
//            'id' => ['required', 'integer', 'exists:users,id'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,'.request()->id],
            'alternate_email' => ['sometimes','required', 'email'],
            'address1' => ['sometimes', 'required'],
            'address2' => ['sometimes', 'nullable'],
            'city' => ['sometimes', 'required'],
            'state' => ['sometimes', 'required'],
            'zip' => ['sometimes', 'required'],
            'job_title' => ['sometimes', 'required'],
            'start_date' => ['sometimes'],
            'end_date' => ['sometimes'],
            'termination_date' => ['sometimes'],
            'notes' => ['sometimes'],
            'client_id' => ['sometimes','string', 'max:255', 'exists:clients,id'],
            'team_id' => ['sometimes', 'required','integer', 'exists:teams,id'],
            'role_id' => ['sometimes', 'required', 'integer'],
            'classification_id' => ['sometimes', 'required'],
            'contact_preference' => ['sometimes', 'nullable'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['sometimes', 'digits:10'], //should be required, but seeders don't have phones.
//            'home_location_id' => ['nullable', 'exists:locations,gymrevenue_id'], //should be required if client_id provided. how to do?
            'home_location_id' => ['sometimes', 'nullable', 'string'], //should be required if client_id provided. how to do?
        ];
    }

    public function handle($id, $data, $current_user)
    {
        $client_id = $current_user->currentClientId();

        if (array_key_exists('password', $data)) {
            $data['password'] = bcrypt($data['password']);
        }

        if (array_key_exists('role_id', $data)) {
            $data['role'] = $data['role_id'];
        }


        UserAggregate::retrieve($id)->updateUser($current_user->id ?? "Auto Generated", $data)->persist();
        if ($client_id) {
            ClientAggregate::retrieve($client_id)->updateUser($current_user->id, $data)->persist();
        }

        return User::find($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('users.update', User::class);
    }

//    public function afterValidator(Validator $validator, ActionRequest $request): void
//    {
//        dd($validator);
//    }

    public function asController(ActionRequest $request, $id)
    {
        $user = $this->handle(
            $id,
            $request->validated(),
            $request->user(),
        );

        Alert::success("User '{$user->name}' was updated")->flash();

//        return Redirect::route('users');
        return Redirect::route('users.edit', $id);
//        return Redirect::back();
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
        if (! array_key_exists('id', $input)) {
            $input['id'] = $user->id;
        }
        $this->run($input, $user);
    }
}
