<?php

namespace Database\Seeders\AccessControl;

use App\Domain\Clients\Models\Client;
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
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);
            collect(SecurityGroupEnum::cases())->keyBy('name')->except('ADMIN')
                ->each(function ($enum) use ($client) {
                    Role::create([
                        'name' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE),
                        'scope' => $client->id ?? null,
                        'group' => $enum->value,
                ])->update(['title' => mb_convert_case(str_replace("_", " ", $enum->name), MB_CASE_TITLE)]);
                });
        }
        Bouncer::scope()->to(null);
    }
}
