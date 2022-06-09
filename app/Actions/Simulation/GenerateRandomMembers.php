<?php

namespace App\Actions\Simulation;

use App\Actions\Endusers\Members\UpsertMemberApi;
use App\Models\Clients\Client;
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
            if (count($client->locations) > 0) {
                foreach ($client->locations as $idx => $location) {
                    $members = Member::factory()->count(random_int(1, 5))
                        ->client_id($client->id)
                        ->gr_location_id($location->gymrevenue_id ?? '')
                        ->make();

                    $leads = Lead::whereClientId($client->id)
                        ->whereGrLocationId($location->gymrevenue_id)
                        ->get();

                    $lead_data = $leads->toArray();
                    $lead = $lead_data[random_int(0, count($lead_data) - 1)];

                    foreach ($members as $member) {
                        $member_data = $member->toArray();
                        foreach ($member_data as $key => $item) {
                            $member_data[$key] = $lead[$key];
                        }
                        UpsertMemberApi::run($member_data);
                    }
                }
            }
        }

        return $members;
    }

    public function asController(ActionRequest $request)
    {
        $this->handle(
            $request->user(),
        );

        Alert::success("Members Randomly Generated")->flash();

        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $members = $this->handle();

        $command->info('done: ');
    }
}
