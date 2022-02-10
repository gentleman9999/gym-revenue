<?php

namespace Database\Seeders;

use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\TeamDetail;
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
        collect(Jetstream::$roles)->each(function ($role) {
            Role::firstOrCreate([
                'name' => $role->key,
            ])->update(['title' => $role->name]);
        });
    }
}
