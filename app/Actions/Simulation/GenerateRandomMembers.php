<?php

namespace App\Actions\Simulation;

use App\Actions\Endusers\Members\BatchUpsertMemberApi;
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
            if (count($client->locations) > 0) {
                foreach ($client->locations as $idx => $location) {
                    //doing this entire block for every location is slow. let's just pick a random 50% of locations for
                    //places with more than 8 locations.
                    if (count($client->locations) > 8 && rand(0, 1) > 0.5) {
                        break;
                    }
                    $members = Member::factory()->count(random_int(1, 5))
                        ->client_id($client->id)
                        ->gr_location_id($location->gymrevenue_id ?? '')
                        ->make();

                    //so just grab a few random
                    $leads = Lead::whereClientId($client->id)
                        ->whereGrLocationId($location->gymrevenue_id)
                        ->inRandomOrder()
                        ->limit(rand(1, 3))
                        ->get();

                    $lead_data = $leads->toArray();

                    $members_with_conversions = $lead_data;
                    foreach ($members as $member) {
                        $member_data = $member->toArray();
                        $members_with_conversions[] = $member_data;
                    }
                    BatchUpsertMemberApi::run($members_with_conversions);
                    $num_members = count($members_with_conversions);
                    VarDumper::dump("Generated {$num_members} Random Members for $client->name at $location->name");
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