<?php

namespace Database\Seeders\Data;

use App\Actions\Endusers\Members\CreateMember;
use App\Models\Clients\Client;
use App\Models\Endusers\Member;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump('Getting Clients');
        // Get all the Clients
        $clients = Client::whereActive(1)
            ->with('locations')
            ->get();

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump($client->name);
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        // For each location, MAKE 25 users, don't create
                        $members = Member::factory()->count(25)
                            // over ride the client id and gr id from the factory
                            ->client_id($client->id)
                            ->gr_location_id($location->gymrevenue_id ?? '')
                            ->make();

                        VarDumper::dump('Generating Members!');
                        foreach ($members as $member) {
//                            $member->membership_type_id = $client->membership_types[random_int(1, count($client->membership_types) - 1)]->id;
                            // @todo - if the lead_source_id is connected to custom, redo it.
                            // @todo - no custom status in the seeder allowed...yet.

                            // For each fake user, run them through the EnduserActivityAggregate
                            $member_data = $member->toArray();

                            CreateMember::run($member_data);

                        }

                    }
                }
            }
        }
    }
}
