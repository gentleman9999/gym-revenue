<?php

namespace App\Actions\Simulation;

use App\Actions\Endusers\Members\UpsertMemberApi;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Endusers\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;
use Symfony\Component\VarDumper\VarDumper;

class GenerateRandomMembers
{
    use AsAction;

    public string $commandSignature = 'generate_random:members';

    public function handle($current_user = null)
    {
        $clients = Client::whereActive(1)
            ->with('locations')
            ->get();
        foreach ($clients as $client) {
            VarDumper::dump($client->name);
            // For each client, get all the locations
            if (count($client->locations) > 0) {
                foreach ($client->locations as $idx => $location) {
                    $members = Member::factory()->count(random_int(1, 5))
                        // over ride the client id and gr id from the factory
                        ->client_id($client->id)
                        ->gr_location_id($location->gymrevenue_id ?? '')
                        ->make();

                    $leads = Lead::whereClientId($client->id)
                        ->whereGrLocationId($location->gymrevenue_id)
                        ->get();

                    $lead_data = $leads->toArray();

                    $lead = $lead_data[random_int(0, count($lead_data) - 1)];

                    //VarDumper::dump('Generating Members for '.$client->name);
                    foreach ($members as $member) {
                        $member_data = $member->toArray();
                        $member_data['first_name'] = $lead['first_name'];
                        $member_data['last_name'] = $lead['last_name'];
                        $member_data['email'] = $lead['email'];
                        $member_data['primary_phone'] = $lead['primary_phone'];
                        $member_data['gender'] = $lead['gender'];
                        UpsertMemberApi::run($member_data);
                    }
                }
            }
        }

        return $members;
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
        $members = $this->handle();

        $command->info('done: ');
    }
}
