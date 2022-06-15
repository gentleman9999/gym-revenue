<?php

namespace App\Actions\Simulation;

use App\Actions\Endusers\Leads\BatchUpsertLeadApi;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Symfony\Component\VarDumper\VarDumper;

class GenerateRandomLeads
{
    use AsAction;

    public string $commandSignature = 'generate_random:leads';

    public function handle($current_user = null)
    {
        $clients = Client::whereActive(1)
            ->with('locations')
            ->with('lead_types')
            ->with('lead_sources')
            ->with('trial_membership_types')
            ->get();
        foreach ($clients as $client) {
            // For each client, get all the locations
            if (count($client->locations) > 0) {
                foreach ($client->locations as $idx => $location) {
                    // For each location, MAKE 25 users, don't create
                    $leads = Lead::factory()->count(random_int(1, 5))
                        // over ride the client id and gr id from the factory
                        ->client_id($client->id)
                        ->gr_location_id($location->gymrevenue_id ?? '')
                        ->make();

                    //VarDumper::dump('Generating Leads for '.$client->name.'!');
                    foreach ($leads as $lead) {
                        $lead->lead_type_id = $client->lead_types[random_int(1, count($client->lead_types) - 1)]->id;
                        $lead->membership_type_id = $client->membership_types[random_int(1, count($client->membership_types) - 1)]->id;
                        $lead->lead_source_id = $client->lead_sources[random_int(1, count($client->lead_sources) - 1)]->id;
                    }
                    BatchUpsertLeadApi::run($leads->toArray());
                    $num_leads = count($leads);
                    VarDumper::dump("Generated {$num_leads} Random Leads for $client->name at $location->name");
                }
            }
        }

        return $leads;
    }

    public function asController(ActionRequest $request)
    {
        $this->handle(
            $request->user(),
        );

        Alert::success("Leads Randomly Generated.")->flash();

        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $prospects = $this->handle();

        $command->info('done: ');
    }
}
