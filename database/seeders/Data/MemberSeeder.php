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
        $amountOfMembers = 3;
        if (env('QUICK_SEED')) {
            $amountOfMembers = 1;
        }
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
                        $members = Member::factory()->count($amountOfMembers)
                            // over ride the client id and gr id from the factory
                            ->client_id($client->id)
                            ->gr_location_id($location->gymrevenue_id ?? '')
                            ->make();

                        VarDumper::dump('Generating Members for '.$client->name);
                        foreach ($members as $member) {
                            $member_data = $member->toArray();
                            CreateMember::run($member_data);
                        }
                    }
                }
            }
        }
    }
}
