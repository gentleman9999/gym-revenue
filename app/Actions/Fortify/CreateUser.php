<?php

namespace App\Actions\Fortify;

use App\Aggregates\CapeAndBay\CapeAndBayUserAggregate;
use App\Aggregates\Clients\ClientAggregate;
use App\Helpers\Uuid;
use App\Models\Clients\Security\SecurityRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
                            {--name= : the name of the user}
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'client_id' => ['sometimes', 'string', 'max:255', 'exists:clients,id'],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'security_role' => ['nullable', 'string', 'max:255', 'exists:security_roles,id'],
//        'security_role' => ['required_with,client_id', 'exists:security_roles,id']
//            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
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

        if ($client_id) {
            ClientAggregate::retrieve($client_id)->createUser($current_user->id ?? "Auto Generated", $data)->persist();
        } else {
            //CapeAndBay User
            CapeAndBayUserAggregate::retrieve($data['team_id'])->createUser($current_user->id ?? "Auto Generated", $data)->persist();
        }
        //TODO:we should use App/Helpers/Uuid to generate an id, but we can use email for now since its unique
        return User::whereEmail($data['email'])->firstOrFail();
    }

    public function asController(Request $request)
    {
        $user = $this->handle(
            $request->all(),
            $request->user(),
        );

        Alert::success("User '{$user->name}' was created")->flash();

        return Redirect::route('users');
    }

    public function asCommand(Command $command): void
    {
        $this->command = $command;
        $user_name = $this->getUsername();
        $email = $this->getEmail($user_name);
        $client = $this->getClient($user_name);
        $role = $this->getRole($user_name, $client);

        $team_id = 1;//capeandbay team
        if($client){
            // Get the client's default-team name in client_details
            $client_model = Client::whereId($client)->with('default_team_name')->first();
            $default_team_name = $client_model->default_team_name->value;
            // Use that to find the team record in teams to get its ID
            $team_id = Team::where('name', '=', $default_team_name)->first()->id;
        }

        $this->command->warn("Creating new {$role} {$user_name} @{$email} for client_id {$client}");
        $this->handle(
            [
                'email' => $email,
                'client_id' => $client,
                'role' => $role,
                'name' => $user_name,
                'password' => 'Hello123!',
                'team_id' => $team_id
            ]
        );
    }

    private function getUsername()
    {
        $name = $this->command->option('name');
        if (is_null($name)) {
            $name = $this->command->ask('Enter the user\'s Full Name');
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
