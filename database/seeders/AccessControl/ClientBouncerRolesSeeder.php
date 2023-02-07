<?php

namespace Database\Seeders\AccessControl;

use App\Domain\Clients\Projections\Client;
use App\Enums\SecurityGroupEnum;
use Bouncer;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;

class ClientBouncerRolesSeeder extends Seeder
{
    protected $teams;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        $groups = SecurityGroupEnum::cases();
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);

            foreach ($groups as $role) {
                if ($role === SecurityGroupEnum::ADMIN) {
                    continue;
                }

                $roles[] = [
                    'name' => $name = mb_convert_case(str_replace('_', ' ', $role->name), MB_CASE_TITLE),
                    'group' => $role->value,
                    'scope' => $client->id,
                    'title' => $name,
                ];
            }
        }

        Role::upsert($roles, ['name', 'group']);
        Bouncer::scope()->to(null);
    }
}
