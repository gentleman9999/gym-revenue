<?php

namespace Database\Seeders\Data;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Models\Clients\Client;
use App\Models\Endusers\Lead;
use App\Models\Endusers\LeadDetails;
use App\Models\Endusers\Service;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class LeadProspectSeeder extends Seeder
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
            ->with('lead_types')
            ->with('lead_sources')
            ->with('membership_types')
            ->with('trial_membership_types')
            ->get();

        $service_ids = Service::all()->pluck('id');

        if (count($clients) > 0) {
            foreach ($clients as $client) {
                VarDumper::dump($client->name);
                // For each client, get all the locations
                if (count($client->locations) > 0) {
                    foreach ($client->locations as $idx => $location) {
                        // For each location, MAKE 25 users, don't create
                        $prospects = Lead::factory()->count(25)
                            // over ride the client id and gr id from the factory
                            ->client_id($client->id)
                            ->gr_location_id($location->gymrevenue_id ?? '')
                            ->make();

                        VarDumper::dump('Generating Leads!');
                        foreach ($prospects as $prospect) {
                            $prospect->lead_type_id = $client->lead_types[random_int(1, count($client->lead_types) - 1)]->id;
                            $prospect->membership_type_id = $client->membership_types[random_int(1, count($client->membership_types) - 1)]->id;
                            $prospect->lead_source_id = $client->lead_sources[random_int(1, count($client->lead_sources) - 1)]->id;
                            $numServices = random_int(0, 4);
                            $services = [];
                            $temp_service_ids = [...$service_ids];

                            for ($x = 0; $x <= $numServices; $x++) {
//                            var_dump($temp_service_ids);
                                $service_index = random_int(0, count($temp_service_ids) - 1);
                                $services[] = $temp_service_ids[$service_index];
                                unset($temp_service_ids[$service_index]);
                                $temp_service_ids = array_values($temp_service_ids);
                            }
                            // For each fake user, run them through the EnduserActivityAggregate
                            $aggy = EndUserActivityAggregate::retrieve($prospect->id);
                            $aggy->createNewLead($prospect->toArray())
                                ->joinAudience('leads', $client->id, Lead::class)
                                ->setServices($services, 'Auto Generated')
                                ->persist();

                            $free_trial_id = $client->lead_types->keyBy('name')['free_trial']->id;

                            if ($prospect->lead_type_id === $free_trial_id) {
                                $aggy->addTrialMembership($client->trial_membership_types[random_int(0, count($client->trial_membership_types) - 1)]->id);
                                $aggy->persist();
                            }

                            if (env('SEED_LEAD_DETAILS', false)) {
                                //only for seeding mass comm lead details for ui dev
                                LeadDetails::factory()->count(random_int(0, 20))->lead_id($prospect->id)->client_id($prospect->client_id)->create();
                            }
                        }

                    }
                }
            }
        }
        /*
         * STEPS
         * 1.
         * 2.
         * 3.
         *      -
         *      -
         * 4.
         */
    }
}
