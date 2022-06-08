<?php

namespace App\Actions\Simulation;

use App\Actions\Endusers\Leads\UpsertLeadApi;
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
            VarDumper::dump($client->name);
            // For each client, get all the locations
            if (count($client->locations) > 0) {
                foreach ($client->locations as $idx => $location) {
                    // For each location, MAKE 25 users, don't create
                    $prospects = Lead::factory()->count(random_int(1, 5))
                        // over ride the client id and gr id from the factory
                        ->client_id($client->id)
                        ->gr_location_id($location->gymrevenue_id ?? '')
                        ->make();

                    //VarDumper::dump('Generating Leads for '.$client->name.'!');
                    foreach ($prospects as $prospect) {
                        $prospect->lead_type_id = $client->lead_types[random_int(1, count($client->lead_types) - 1)]->id;
                        $prospect->membership_type_id = $client->membership_types[random_int(1, count($client->membership_types) - 1)]->id;
                        $prospect->lead_source_id = $client->lead_sources[random_int(1, count($client->lead_sources) - 1)]->id;
                        $prospect_data = $prospect->toArray();

                        try {
                            $lead = UpsertLeadApi::run($prospect_data);
                            //VarDumper::dump('Success '.$client->name.'!');
                        } catch (\Exception $e) {
                            //VarDumper::dump('Failed: '.$e);
                        }
                    }
                }
            }
        }

        return $prospects;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('access_token.create', Location::class);
    }

    public function asController(ActionRequest $request)
    {
        $this->handle(
            $request->user(),
        );

        Alert::success("Access Token Generated & granted.")->flash();

        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $prospects = $this->handle();

        $command->info('done: ');
    }
}
