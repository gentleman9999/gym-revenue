<?php

namespace Database\Seeders;

use Database\Seeders\Clients\ClientSeeder;
use Database\Seeders\Users\CapeAndBayUserSeeder;
use Database\Seeders\Users\ClientUserSeeder;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

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
    }
}
