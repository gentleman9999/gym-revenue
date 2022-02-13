<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\TeamDetail;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class SecurityRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        $roles = Role::all()->keyBy('name');
        $security_roles = [
            [
                'security_role' => 'Account Owner',
                'role' => 'Account Owner',
            ],
            [
                'security_role' => 'Location Manager - GM',
                'role' => 'Location Manager',
            ],
            [
                'security_role' => 'Region Admin - District Mgr',
                'role' => 'Regional Admin',
            ],
            [
                'security_role' => 'Sales Rep - InStore Sales',
                'role' => 'Sales Rep',
            ],
            [
                'security_role' => 'Employee',
                'role' => 'Employee',
            ],
        ];
        $clients->each(function ($client) use ($security_roles, $roles) {
            foreach ($security_roles as $security_role) {
                VarDumper::dump("Creating Security Role '{$security_role['security_role']}' for {$client->name}");
                //TODO: logic for setting default ability ids
                SecurityRole::create([
                    'client_id' => $client->id,
                    'role_id' => $roles[$security_role['role']]->id,
                    'security_role' => $security_role['security_role'],
                    'ability_ids' => []
                ]);
            }
        });
    }
}
