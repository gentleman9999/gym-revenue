<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\TeamDetail;
use Illuminate\Database\Seeder;
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
        $security_roles = [
            [
                'security_role' => 'Location Manager - GM',
                'role_id' => 3,
            ],
            [
                'security_role' => 'Region Admin - District Mgr',
                'role_id' => 5,
            ],
            [
                'security_role' => 'Sales Rep - InStore Sales',
                'role_id' => 4,
            ],
        ];
        $clients->each(function ($client) use ($security_roles) {
            foreach ($security_roles as $security_role) {
                VarDumper::dump("Creating Security Role '{$security_role['security_role']}' for {$client->name}");
                SecurityRole::create(array_merge($security_role, ['client_id' => $client->id, 'ability_ids' => []]));
            }
        });


    }
}
