<?php

namespace Database\Seeders\Data;

use App\Models\Clients\Client;
use App\Models\Endusers\MembershipType;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $membership_types = ['Prospect', 'Lead', 'Guest Pass', 'Membership1', 'Membership2', 'Membership3', 'Membership4'];
        $clients = Client::all();
        foreach ($clients as $client) {
            foreach ($membership_types as $membership_type) {
                MembershipType::create(['client_id' => $client->id, 'name' => $membership_type]);
                VarDumper::dump("Adding membership type {$membership_type}");
            }
        }
    }

}
