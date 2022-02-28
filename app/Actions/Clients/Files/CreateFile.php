<?php

namespace App\Actions\Clients\Files;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use App\Models\Clients\Location;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Console\Command;


class CreateFile
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'altEmail' => ['sometimes', 'required', 'email'],
            'address1' => ['required'],
            'address2' => ['sometimes', 'nullable'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'jobTitle' => ['required'],
            'notes' => ['sometimes'] ,
            'start_date' => ['sometimes'] ,
            'end_date' => ['sometimes'] ,
            'termination_date' => ['sometimes'] ,
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],

            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'security_role' => ['nullable', 'string', 'max:255', 'exists:security_roles,id'],
//        'security_role' => ['required_with,client_id', 'exists:security_roles,id']
//            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['sometimes', 'digits:10'], //should be required, but seeders don't have phones.
            'home_club' => ['sometimes', 'exists:locations,gymrevenue_id'] //should be required if client_id provided. how to do?

        ];
    }

    public function handle($data, $current_user = null)
    {
        if ($current_user) {
            $client_id = $current_user->currentClientId();
        } else {
            $client_id = $data['client_id'];
        }

//        $id = Uuid::new();//we should use uuid here, but then we'd have to change all the bouncer tables to use uuid instead of bigint;
        $id = (User::max('id') ?? 0) + 1;
        $data['id'] = $id;

        $user_aggy = UserAggregate::retrieve($id)
            ->createUser($current_user->id ?? "Auto Generated", $data);

        $user_teams = $data['team_ids'] ?? [$data['team_id']] ?? [];

        $user_aggy->persist();
        if ($client_id) {
            ClientAggregate::retrieve($id)->createUser($current_user->id ?? "Auto Generated", $data)->persist();
        }
        //we should use App/Helpers/Uuid to generate an id, but we can use email for now since its unique
        $created_user = User::findOrFail($id);

        return $created_user;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('files.create', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {

        $file = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("File '{$file->name}' was created")->flash();

//        return Redirect::route('files');
        return Redirect::back();

    }
}
