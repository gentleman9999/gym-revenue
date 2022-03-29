<?php

namespace Database\Seeders\AccessControl;

use App\Models\Clients\Client;
use Illuminate\Database\Seeder;
use Bouncer;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class BouncerRolesSeeder extends Seeder
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
            collect(Jetstream::$roles)->each(function ($role) use ($client){
                Role::create([
                    'name' => $role->key,
                    'client_id' => $client->id ?? null,
                ])->update(['title' => $role->name]);
            });
        }
    }
}
