<?php

namespace Database\Seeders\AccessControl;

use Illuminate\Database\Seeder;
use Bouncer;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class CapeAndBayBouncerRolesSeeder extends Seeder
{
    protected $teams;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(Jetstream::$roles)->only(['Admin'])->each(function ($role) {
            Role::create([
                'name' => $role->key,
                'client_id' => null,
            ])->update(['title' => $role->name]);
        });
    }

}
