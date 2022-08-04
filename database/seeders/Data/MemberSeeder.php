<?php

namespace Database\Seeders\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Members\Actions\CreateMember;
use App\Domain\EndUsers\Members\Projections\Member;
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
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        $members = Member::factory()->count($amountOfMembers)
                            ->client_id($client->id)
                            ->gr_location_id($location->gymrevenue_id ?? '')
                            ->make();

                        VarDumper::dump('Generating Members for '.$client->name);
                        foreach ($members as $member) {
                            $member_data = $member->toArray();
                            $member_data['gr_location_id'] = $location->gymrevenue_id;
                            $member_data['client_id'] = $client->id;
                            CreateMember::run($member_data);
                            //UpsertMemberApi::run($member_data);
                        }
                    }
                }
            }
        }
    }
}
