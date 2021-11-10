<?php

namespace Database\Seeders;

use Database\Seeders\Clients\TeamLocationsSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\Clients\ClientSeeder;
use Symfony\Component\VarDumper\VarDumper;
use Database\Seeders\Users\ClientUserSeeder;
use Database\Seeders\Clients\LocationSeeder;
use Database\Seeders\Users\CapeAndBayUserSeeder;
use Database\Seeders\Clients\SecondaryTeamsSeeder;
use Database\Seeders\Users\SecondaryClientUsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        VarDumper::dump('Creating Cape & Bay Users');
        $this->call(CapeAndBayUserSeeder::class);

        VarDumper::dump('Running Client Seeder');
        $this->call(ClientSeeder::class);

        VarDumper::dump('Running Client User Seeder');
        $this->call(ClientUserSeeder::class);

        VarDumper::dump('Running Client Location Seeder');
        $this->call(LocationSeeder::class);

        VarDumper::dump('Running Client Secondary Teams Seeder');
        $this->call(SecondaryTeamsSeeder::class);

        VarDumper::dump('Running Client Secondary Users Seeder');
        $this->call(SecondaryClientUsersSeeder::class);

        VarDumper::dump('Running Client Team/Location Assignments Seeder');
        $this->call(TeamLocationsSeeder::class);
    }
}
