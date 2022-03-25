<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Clients\Classification;
use App\Models\Endusers\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class ClassificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        $classifications = [
            'Club Associate',
            'Floor Manager/Team Lead',
            'Fitness Trainer',
            'Personal Trainer',
            'Instructor',
            'Sanitation'
        ];

        foreach ($classifications as $classification)
        {
            Classification::create([
                //'client_id' => $client->id,
                'title' => $classification,
            ]);
        }

    }
}
