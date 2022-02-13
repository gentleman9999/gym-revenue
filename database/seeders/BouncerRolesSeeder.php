<?php

namespace Database\Seeders;

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
//            dd(Bouncer::role()->keyIsUuid());
//            Bouncer::role()::firstOrCreate([
//                'name' => $role->key,
//            ])->update(['title' => $role->name]);
            Bouncer::role()::create([
                'name' => $role->key,
            ])->update(['title' => $role->name]);
        });
    }
}
