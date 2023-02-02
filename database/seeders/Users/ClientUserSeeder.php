<?php

namespace Database\Seeders\Users;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use App\Services\Process;
use Illuminate\Database\Seeder;

class ClientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $number_of_sales_rep = env('QUICK_SEED') ? 1 : 5;
        $clients = Client::with(['teams', 'departments.positions', 'roles'])->get()->toArray();
        $process = Process::allocate(5);

        foreach ($clients as $client) {
            $process->queue([self::class, 'createUser'], $number_of_sales_rep, $client);
//            call_user_func([self::class, 'createUser'], $number_of_sales_rep, $client);
        }

        $process->run();
    }

    public static function createUser(int $number_of_sales_rep, array $client): void
    {
        $departments = $client['departments'];
        $team_names = array_column($client['teams'], 'name');

        foreach ($client['roles'] as $role) {
            if ($role['title'] !== 'Employee') {
                if ($role['title'] === 'Sales Rep') {
                    $users = User::factory()
                        ->count($number_of_sales_rep)
                        ->make([
                            'client' => $client['name'],
                            'role_id' => $role['id'],
                            'team_names' => $team_names,
                        ]);
                } else {
                    $users = User::factory()
                        ->count(1)
                        ->make([
                            'client' => $client['name'],
                            'role_id' => $role['id'],
                            'team_names' => $team_names,
                        ]);
                }

                $department_count = count($departments);
                foreach ($users as $user) {
                    $department = $departments[rand(0, $department_count - 1)];
                    $positions = array_column($department['positions'], 'id');
                    $position_id = null;

                    while (count($positions) === 0) {
                        $department = $departments[rand(0, $department_count - 1)];
                        $positions = array_column($department['positions'], 'id');
                    }

                    $position_id = $positions[array_rand($positions)];

                    $client = Client::whereName($user['client'])->first();
                    $teams = Team::whereIn('name', $user['team_names'])->get();

                    $team_ids = [];
                    $possible_home_locations = [];
                    foreach ($teams as $team) {
                        $team_ids[] = $team->id;
                        $possible_home_locations = array_merge($possible_home_locations, $team->locations());
                    }

                    if (count($possible_home_locations) > 0) {
                        $home_location_id = $possible_home_locations[array_rand($possible_home_locations)];
                    } else {
                        $home_location_id = null;
                    }
                    $senior_managers = [3, 2, 1];
                    $managers = [4];
                    $manager = in_array($user['role_id'], $senior_managers) ? 'Senior Manager' : (in_array($user['role_id'], $managers) ? 'Manager' : null);
                    CreateUser::run(array_merge($user->toArray(), [
                        'client_id' => $client->id,
                        'password_hashed' => '$2y$10$S9YUvKwjvgTuj8K5wXlqTOBUDJ0hOEk/dhBoVAm/vYVchKS/taQt2', // Hello123!,
                        'team_ids' => $team_ids,
                        'home_location_id' => $home_location_id,
                        'manager' => $manager,
                        'user_type' => UserTypesEnum::EMPLOYEE,
                        'departments' => [
                            ['department' => $department['id'], 'position' => $position_id],
                        ],
                    ]));
                }
            }
        }

        echo "Added {$client['name']} Users\n";
    }
}
