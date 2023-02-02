<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Models\Endusers\MembershipType;
use Illuminate\Database\Seeder;

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
        $data = [];
        foreach ($clients as $client) {
            foreach ($membership_types as $membership_type) {
                $data[] = ['id' => Uuid::new(), 'client_id' => $client->id, 'name' => $membership_type];
                echo("Adding membership type {$membership_type}\n");
            }
        }

        MembershipType::insert($data);
    }
}
