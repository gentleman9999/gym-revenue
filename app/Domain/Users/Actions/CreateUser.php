<?php

namespace App\Domain\Users\Actions;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Roles\Role;
use App\Domain\Teams\Actions\AddTeamMember;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Domain\Users\ValidationRules;
use App\Enums\UserTypesEnum;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Console\Command;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateUser implements CreatesNewUsers
{
    use AsAction;

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
                            {--homeclub= : the gymrevenue_id of the home_club to assign }
                            {--type= : the type of user }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    public string $commandDescription = 'Create a new user via the CLI.';

    public function handle(array $payload): User
    {
        if (array_key_exists('password', $payload)) {
            $payload['password'] = Hash::make($payload['password']);
        }

        $id = Uuid::new();
        $user_aggy = UserAggregate::retrieve($id)->create($payload)->persist();
        $created_user = User::findOrFail($id);
        if ($this->getUserTypeFromPayload($created_user, $payload) == UserTypesEnum::EMPLOYEE) {
            $user_teams = $payload['team_ids'] ?? (array_key_exists('team_id', $payload) ? [$payload['team_id']] : []);
            foreach ($user_teams as $i => $team_id) {
                /**
                 * Since the user needs to have their team added in a single transaction in createUser
                 * A projector won't get executed (for now) but an apply function will run on the next retrieval
                 */

                // $team_name = Team::getTeamName($team_id);
                AddTeamMember::run(Team::findOrFail($team_id), $created_user);
                // $team_client = Team::getClientFromTeamId($team_id);
                // $team_client_id = ($team_client) ? $team_client->id : null;
                // $user_aggy = $user_aggy->addToTeam($team_id, $team_name, $team_client_id);
            }
        }

        /** @TODO:checkbox on create userform to send email or not */
        if (isset($payload['send_welcome_email'])) {
            UserAggregate::retrieve($created_user->id)->sendWelcomeEmail()->persist();
        }

        ReflectUserData::run($created_user);

        return $created_user;
    }

    public function __invoke($_, array $args): User
    {
        if ($args['input']['start_date']) {
            $args['input']['start_date'] = CarbonImmutable::create($args['input']['start_date']);
        } else {
            $args['input']['start_date'] = null;
        }
        if ($args['input']['end_date']) {
            $args['input']['end_date'] = CarbonImmutable::create($args['input']['end_date']);
        } else {
            $args['input']['end_date'] = null;
        }
        if ($args['input']['termination_date']) {
            $args['input']['termination_date'] = CarbonImmutable::create($args['input']['termination_date']);
        } else {
            $args['input']['termination_date'] = null;
        }

        return $this->handle($args['input']);
    }

    /**
     * Custom validation based on user_type
     *
     * @return array
     */
    public function rules(): array
    {
        $current_route = Route::currentRouteName();

        if ($current_route == 'users.store') {
            $user_type = UserTypesEnum::EMPLOYEE;
        } elseif ($current_route == 'data.leads.store') {
            $user_type = UserTypesEnum::LEAD;
        } elseif ($current_route == 'data.members.store') {
            $user_type = UserTypesEnum::MEMBER;
        } else {
            $user_type = UserTypesEnum::CUSTOMER;
        }

        return ValidationRules::getValidationRules($user_type, true);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    /**
     * Transform the request object in
     * preparation for validation
     *
     * @param ActionRequest $request
     */
    public function prepareForValidation(ActionRequest $request): void
    {
        $request = $this->mergeUserTypeToRequest($request);

        if ($request->user_type == UserTypesEnum::LEAD) {
            /** @TODO: Need to update with what entry_source data should be */
            $request->merge(['entry_source' => json_encode(['id' => 'some id', 'metadata' => ['something' => 'yes', 'something_else' => 'also yes']])]);
        }

        $request->mergeIfMissing([
            'started_at' => $request->start_date,
            'ended_at' => $request->end_date,
            'terminated_at' => $request->termination_date,
        ]);
    }

    /**
     * Adds the user type to the request object
     * based on the route name of the request
     *
     * @param ActionRequest $request
     *
     * @return ActionRequest $request
     */
    private function mergeUserTypeToRequest(ActionRequest $request): ActionRequest
    {
        $current_route = Route::currentRouteName();

        if ($current_route == 'users.store') {
            $request->merge(['user_type' => UserTypesEnum::EMPLOYEE]);
        } elseif ($current_route == 'data.leads.store') {
            $request->merge(['user_type' => UserTypesEnum::LEAD]);
        } elseif ($current_route == 'data.members.store') {
            $request->merge(['user_type' => UserTypesEnum::MEMBER]);
        } else {
            $request->merge(['user_type' => UserTypesEnum::CUSTOMER]);
        }

        return $request;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $request->user_type == UserTypesEnum::EMPLOYEE ?
            $current_user->can('users.create', User::class) :
            $current_user->can('endusers.create', EndUser::class);
    }

    public function asController(ActionRequest $request): User
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(User $user): RedirectResponse
    {
        $type = $user->user_type == UserTypesEnum::EMPLOYEE->value ? 'User' : ucwords($user->user_type->value);
        Alert::success("{$type} '{$user->name}' was created")->flash();

        return $user->user_type == UserTypesEnum::EMPLOYEE ?
            Redirect::route('users.edit', $user->id) :
            Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $this->command = $command;
        $payload = $this->constructPayloadForRunningCommand();
        $role = $payload['rold_id'] ?? 'enduser';
        $user_type = $this->getUserTypeFromPayload(null, $payload)->value;

        $this->command->warn("Creating new {$role} {$payload['first_name']}
            @{$payload['email']} for client_id {$payload['client_id']} as {$user_type}");
        $this->handle($payload);
    }

    /**
     * Constructs an assoc array to be used as the payload
     * when creating user using artisan command
     *
     * @return array $payload
     */
    private function constructPayloadForRunningCommand(): array
    {
        $payload = ['password' => 'Hello123!'];
        $payload['first_name'] = $this->getFirstname();
        $payload['client_id'] = $this->getClient($payload['first_name']);
        $payload['email'] = $this->getEmail($payload['first_name']);
        $payload['last_name'] = $this->getLastname();
        $payload['home_club'] = $this->getHomeLocation($payload['first_name'], $payload['client_id']);
        $payload['user_type'] = $this->getUserType();

        if ($payload['user_type'] === UserTypesEnum::EMPLOYEE) {
            $payload['role_id'] = $this->getRole($payload['first_name'], $payload['client_id']);
            $payload['team_id'] = $this->getUserTeamID($payload['client_id']);
        }

        return $payload;
    }

    private function getFirstname(): string
    {
        $name = $this->command->option('firstname');
        if (is_null($name)) {
            $name = $this->command->ask('Enter the user\'s first name');
        }

        return $name;
    }

    private function getLastname(): string
    {
        $name = $this->command->option('lastname');
        if (is_null($name)) {
            $name = $this->command->ask('Enter the user\'s last name');
        }

        return $name;
    }

    private function getEmail(string $user_name): string
    {
        $email = $this->command->option('email');
        if (is_null($email)) {
            $email = $this->command->ask("Enter the {$user_name}'s Email Address");
        }

        return $email;
    }

    private function getClient(string $user_name): ?string
    {
        $client_id = $this->command->option('client');

        if (! is_null($client_id)) {
            if ($client_id === "0") {
                return null;
            } else {
                $client_model = Client::find($client_id);
                if (! is_null($client_model)) {
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

        foreach ($db_clients as $idx => $client) {
            $clients[$idx + 1] = $client->name;
            $client_ids[$idx + 1] = $client->id;
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

    private function getRole(string $user_name, string $client_choice = null): Role
    {
        $selected_role = $this->command->option('role');

        if (is_null($selected_role)) {
            $roles = Role::whereScope($client_choice)->get()->pluck('name')->toArray();

            foreach ($roles as $idx => $role) {
                $this->command->warn("[{$idx}] {$role}");
            }
            $role_choice = $this->command->ask("Which Role should {$user_name} be assigned?");
            $selected_role = Role::whereScope($client_choice)->whereName($roles[$role_choice])->first()->id;
        }

        return $selected_role;
    }

    private function getHomeLocation(string $user_name, string $client_choice = null)
    {
        $selected_home_location = $this->command->option('homeclub');

        if (is_null($selected_home_location) && $client_choice) {
            $all_locations = Location::get(['name', 'gymrevenue_id']);
            $locations = $all_locations->pluck('name')->toArray();

            $location_choice = $this->command->choice("Which home club should {$user_name} be assigned to?", $locations);
            $selected_home_location = $all_locations->keyBy('name')[$location_choice]->gymrevenue_id;
        }

        return $selected_home_location;
    }

    private function getUserType(): string
    {
        $types = array_column(UserTypesEnum::cases(), 'value');
        $user_type = $this->command->option('type');
        if (! in_array($user_type, $types)) {
            return UserTypesEnum::EMPLOYEE;
        }

        return $user_type;
    }

    /**
     * Get the client's default-team name in client_details
     * and use that to find the team record in teams to
     * get its ID, otherwise return capeandbay team
     *
     * @param string $client
     *
     * @return string $team_id
     */
    private function getUserTeamID(string $client): string
    {
        if ($client) {
            $client_model = Client::whereId($client)->first();

            return Team::find($client_model->home_team_id)->id;
        }

        return 1;
    }

    /**
     * Create a newly registered user  (fortify contract).
     *
     * @param array $input
     * @return User
     */
    public function create(array $input)
    {
        return $this->handle($input);
    }

    protected function getUserTypeFromPayload(?User $user, array $data): UserTypesEnum
    {
        if ($user !== null) {
            $default_type = $user->user_type;
        } else {
            $default_type = UserTypesEnum::LEAD;
        }

        if (array_key_exists('user_type', $data)) {
            if (gettype($data['user_type']) == 'string') {
                $data['user_type'] = UserTypesEnum::getByValue($data['user_type']);
            }

            $user_type = array_key_exists('user_type', $data) ?
                $data['user_type'] : $default_type;
        }

        return in_array($user_type, UserTypesEnum::cases()) ?
            $user_type : UserTypesEnum::LEAD;
    }
}
