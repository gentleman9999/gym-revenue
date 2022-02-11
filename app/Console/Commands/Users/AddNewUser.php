<?php

namespace App\Console\Commands\Users;

use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Clients\Client;
use Illuminate\Console\Command;
use Silber\Bouncer\BouncerFacade as Bouncer;

class AddNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:new
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
    protected $description = 'Create a new user via the CLI.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $user_name = $this->getUsername();
        $email = $this->getEmail($user_name);
        $client = $this->getClient($user_name);
        $role = $this->getRole($user_name, $client);

        $this->warn("Creating new {$role} {$user_name} @{$email} for client_id {$client}");
        $this->createUser($user_name, $email, $role, $client);
    }

    private function getUsername()
    {
        $name = $this->option('name');
        if(is_null($name))
        {
            $name = $this->ask('Enter the user\'s Full Name');
        }

        return $name;
    }

    private function getEmail(string $user_name)
    {
        $email = $this->option('email');
        if(is_null($email))
        {
            $email = $this->ask("Enter the {$user_name}'s Email Address");
        }

        return $email;
    }

    private function getClient(string $user_name)
    {
        $client = $this->option('client');

        if(!is_null($client))
        {
            if($client === "0")
            {
                return null;
            }
            else
            {
                $client_model = Client::find($client);
                if(!is_null($client_model))
                {
                    return $client_model->id;
                }
                else
                {
                    $this->error('Invalid Client. Pick one.');
                    sleep(2);
                }
            }
        }


        $clients = ['Cape & Bay'];
        $client_ids = [];
        $db_clients = Client::whereActive(1)->get();
        foreach ($db_clients as $idx => $client)
        {
            $clients[$idx + 1] = $client->name;
            $client_ids[$idx + 1] = $client->id;
        }
        $this->info('Associate an Account with this user.');
        foreach($clients as $idx => $name)
        {
            $this->warn("[{$idx}] {$name}");
        }
        $client_choice = $this->ask("Which client to associate {$user_name} with?");

        if($client_choice > 0)
        {
            $client = $client_ids[$client_choice];
            $this->info($clients[$client_choice]);
        }
        else
        {
            $this->info('Selected Cape & Bay');
        }

        return $client;
    }

    //TODO: we can pull from Roles table instead of hardcoding.
    private function getRole(string $user_name, string $client_choice = null)
    {
        $selected_role = $this->option('role');

        if(is_null($selected_role))
        {
            $roles = [];
            if(!is_null($client_choice))
            {
                $roles[] = 'Account Owner';
                $roles[] = 'Regional Admin';
                $roles[] = 'Location Manager';
                $roles[] = 'Sales Rep';
                $roles[] = 'Employee';
            }
            else
            {
                $roles[] = 'Admin';
            }

            foreach($roles as $idx => $role)
            {
                $this->warn("[{$idx}] {$role}");
            }
            $role_choice = $this->ask("Which Role should {$user_name} be assigned?");
            $selected_role = $roles[$role_choice];
        }

        return $selected_role;
    }

    private function createUser(string $user_name, string $email, string $role, string $client = null)
    {
        $user = User::create([
            'name' => $user_name,
            'email' => $email,
            'password' => bcrypt('Hello123!')
        ]);
        if(is_null($client))
        {
            $this->createCapeAndBayUser($user, $role);
        }
        else
        {
            $this->createClientUser($user, $client, $role);
        }
    }
    private function createCapeAndBayUser(User $user, string $role)
    {
        // set default_team to 1 in user_details
        UserDetails::create([
            'user_id' => $user->id,
            'name' => 'default_team',
            'value' => 1,
            'active' => 1
        ]);

        // set team_user record to 1 (or use an action if possible)
        $team = Team::find(1);
        $team->users()->attach(
            $user, ['role' => $role]
        );
        // Use Bouncer to assign the Admin Role
        Bouncer::assign($role)->to($user);

        $this->info('Created! Checking out...');
    }

    private function createClientUser(User $user, string $client, string $role)
    {
        // set associated_client to $client in user_details
        UserDetails::create([
            'user_id' => $user->id,
            'name' => 'associated_client',
            'value' => $client,
            'active' => 1
        ]);

        // Get the client's default-team name in client_details
        $client_model = Client::whereId($client)->with('default_team_name')->first();
        $default_team_name = $client_model->default_team_name->value;
        // Use that to find the team record in teams to get its ID
        $team = Team::where('name', '=', $default_team_name)->first();

        // Set default_team to $client's default-team's team_id in user_details
        UserDetails::create([
            'user_id' => $user->id,
            'name' => 'default_team',
            'value' => $team->id,
            'active' => 1
        ]);

        // set team_user record to $client's default-team's team_id (or use an action if possible)
        $team->users()->attach(
            $user, ['role' => $role]
        );
        // Use Bouncer to assign the Admin Role
        Bouncer::assign($role)->to($user);

        $this->info('Created! Checking out...');
    }
}
