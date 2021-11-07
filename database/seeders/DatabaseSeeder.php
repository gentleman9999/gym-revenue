<?php

namespace Database\Seeders;

use Database\Seeders\Clients\ClientSeeder;
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
        VarDumper::dump('Running Client Seeder');
        $this->call(ClientSeeder::class);
    }
}
