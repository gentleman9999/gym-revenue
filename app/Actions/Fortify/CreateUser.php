<?php

namespace App\Actions\Fortify;

use App\Aggregates\Clients\ClientAggregate;
use App\Aggregates\Users\UserAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;
use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\User;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Console\Command;


class CreateUser implements CreatesNewUsers
{
    use PasswordValidationRules, AsAction;

    protected $command;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public string $commandSignature = 'user:create
                            {--firstname= : the first name of the user}
                            {--lastname= : the last name of the user}
                            {--email= : the email of the user}
                            {--role= : the role of the user}
                            {--client= : the uuid of the client to assign }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    public string $commandDescription = 'Create a new user via the CLI.';

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
            'altEmail' => ['sometimes','required', 'email'],
            'address1' => ['required'],
            'address2' => ['sometimes', 'nullable'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'jobTitle' => ['required'],
            'notes' => [] ,
            'start_date' => '',
            'end_date' => '',
            'termination_date' => '',
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'security_role' => ['nullable', 'string', 'max:255', 'exists:security_roles,id'],
//        'security_role' => ['required_with,client_id', 'exists:security_roles,id']
//            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['sometimes', 'digits:10'] //should be required, but seeders don't have phones.

        ];
    }

    public function handle($data, $current_user = null)
    {
        if ($current_user) {
            $client_id = $current_user->currentClientId();
        } else {
            $client_id = $data['client_id'];
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = bcrypt($data['password']);
        }

//        $id = Uuid::new();//we should use uuid here, but then we'd have to change all the bouncer tables to use uuid instead of bigint;
        $id = (User::max('id') ?? 0) + 1;
        $data['id'] = $id;

        $user_aggy = UserAggregate::retrieve($id)
            ->createUser($current_user->id ?? "Auto Generated", $data);

        $user_teams = $data['team_ids'] ?? [$data['team_id']] ?? [];
        foreach ($user_teams as $i => $team_id)
        {
            // Since the user needs to have their team added in a single transaction in createUser
            // A projector won't get executed (for now) but an apply function will run on the next retrieval
            $team_name = Team::getTeamName($team_id);
            $team_client = Team::getClientFromTeamId($team_id);
            $team_client_id = ($team_client) ? $team_client->id : null;
            $user_aggy = $user_aggy->addUserToTeam($team_id, $team_name, $team_client_id);
        }

        $user_aggy->persist();
        if ($client_id) {
            ClientAggregate::retrieve($id)->createUser($current_user->id ?? "Auto Generated", $data)->persist();
        }
        //we should use App/Helpers/Uuid to generate an id, but we can use email for now since its unique
        $created_user = User::findOrFail($id);

        $should_send_welcome_email = $data['send_welcome_email'] ?? false;//TODO:checkbox on create userform to send email or not
        if ($should_send_welcome_email) {
            UserAggregate::retrieve($created_user->id)->sendWelcomeEmail()->persist();
        }

        return $created_user;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('users.create', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {

        $user = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("User '{$user->name}' was created")->flash();

        return Redirect::route('users');
    }

    public function asCommand(Command $command): void
    {
        $this->command = $command;
        $first_name = $this->getFirstname();
        $last_name = $this->getLastname();
        $email = $this->getEmail($first_name);
        $client = $this->getClient($first_name);
        $role = $this->getRole($first_name, $client);

        $team_id = 1;//capeandbay team
        if ($client) {
            // Get the client's default-team name in client_details
            $client_model = Client::whereId($client)->with('default_team_name')->first();
            $default_team_name = $client_model->default_team_name->value;
            // Use that to find the team record in teams to get its ID
            $team_id = Team::find($default_team_name)->id;
            //$team_id = Team::where('name', '=', $default_team_name)->first()->id;
        }

        $this->command->warn("Creating new {$role} {$first_name} @{$email} for client_id {$client}");
        $this->handle(
            [
                'email' => $email,
                'client_id' => $client,
                'role' => $role,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'password' => 'Hello123!',
                'team_id' => $team_id
            ]
        );
    }

    private function getFirstname()
    {
        $name = $this->command->option('firstname');
        if (is_null($name)) {
            $name = $this->command->ask('Enter the user\'s first name');
        }

        return $name;
    }

    private function getLastname()
    {
        $name = $this->command->option('lastname');
        if (is_null($name)) {
            $name = $this->command->ask('Enter the user\'s last name');
        }

        return $name;
    }

    private function getEmail(string $user_name)
    {
        $email = $this->command->option('email');
        if (is_null($email)) {
            $email = $this->command->ask("Enter the {$user_name}'s Email Address");
        }

        return $email;
    }

    private function getClient(string $user_name)
    {
        $client_id = $this->command->option('client');

        if (!is_null($client_id)) {
            if ($client_id === "0") {
                return null;
            } else {
                $client_model = Client::find($client_id);
                if (!is_null($client_model)) {
                    return $client_model->id;
                } else {
                    $this->command->error('Invalid Client. Pick one.');
                    sleep(2);
                }
            }
        }


        $clients = ['Cape & Bay'];
        $client_ids = [];
        $db_clients = Client::whereActive(1)->get();
        foreach ($db_clients as $idx => $client_id) {
            $clients[$idx + 1] = $client_id->name;
            $client_ids[$idx + 1] = $client_id->id;
        }
        $this->command->info('Associate an Account with this user.');
        foreach ($clients as $idx => $name) {
            $this->command->warn("[{$idx}] {$name}");
        }
        $client_choice = $this->command->ask("Which client to associate {$user_name} with?");

        if ($client_choice > 0) {
            $client_id = $client_ids[$client_choice];
            $this->command->info($clients[$client_choice]);
        } else {
            $this->command->info('Selected Cape & Bay');
        }

        return $client_id;
    }

    private function getRole(string $user_name, string $client_choice = null)
    {
        $selected_role = $this->command->option('role');

        if (is_null($selected_role)) {
            $roles = [];
            if (!is_null($client_choice)) {
                $roles[] = 'Account Owner';
                $roles[] = 'Regional Admin';
                $roles[] = 'Location Manager';
                $roles[] = 'Sales Rep';
            } else {
                $roles[] = 'Admin';
            }

            foreach ($roles as $idx => $role) {
                $this->command->warn("[{$idx}] {$role}");
            }
            $role_choice = $this->command->ask("Which Role should {$user_name} be assigned?");
            $selected_role = $roles[$role_choice];
        }

        return $selected_role;
    }


    /**
     * Create a newly registered user  (fortify contract).
     *
     * @param array $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $this->run($input);
    }

}
