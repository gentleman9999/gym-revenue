<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Features\ClientService;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class ClientSettingsController extends Controller
{

    public function index()
    {
        $client_id = request()->user()->currentClientId();
        if(!$client_id){
            return Redirect::route('dashboard');
        }
        $client = Client::with(['details', 'trial_membership_types', 'locations'])->find($client_id);

        $test = [
            ['name' => 'SMS', 'value' => 'SMS'],
            ['name' => 'EMAIL', 'value' => 'EMAIL'],
        ];
        return Inertia::render('ClientSettings/Index', [
            'availableServices' => ClientService::whereClientId($client_id)->get(['feature_name', 'slug', 'id']) ?? [],
            'commPreferences' => $test,
            'services' => $client->services ?? [],
            'trialMembershipTypes' => $client->trial_membership_types ?? [],
            'locations' => $client->locations ?? []
        ]);
    }

    public function updateClientServices()
    {
        $data = request()->validate(['services' => ['sometimes', 'array']]);
        $client_id = request()->user()->currentClientId();
        if (array_key_exists('services', $data) && is_array($data['services'])) {
            ClientAggregate::retrieve($client_id)->setClientServices($data['services'], request()->user()->id)->persist();
        }
        Alert::success("Client Services updated.")->flash();

        return Redirect::back();
    }

    public function updateTrialMembershipTypes()
    {
        $data = request()->validate([
            'trialMembershipTypes' => ['sometimes', 'array'],
            'trialMembershipTypes.*.type_name' => ['required'],
            'trialMembershipTypes.*.slug' => ['required'],
            'trialMembershipTypes.*.trial_length' => ['required'],
        ]);
        $client_id = request()->user()->currentClientId();
        if (array_key_exists('trialMembershipTypes', $data) && is_array($data['trialMembershipTypes'])) {
            $trialMembershipTypesToUpdate = collect($data['trialMembershipTypes'])->filter(function ($t) {
                return $t['id'] !== null;
            });
            $trialMembershipTypesToCreate = collect($data['trialMembershipTypes'])->filter(function ($t) {
                return $t['id'] === null;
            });

            $client_aggy = ClientAggregate::retrieve($client_id);

            foreach ($trialMembershipTypesToUpdate as $trialMembershipTypeData) {
                $client_aggy->updateTrialMembershipType($trialMembershipTypeData, request()->user()->id);
            }
            foreach ($trialMembershipTypesToCreate as $trialMembershipTypeData) {
                $client_aggy->createTrialMembershipType($trialMembershipTypeData, request()->user()->id);
            }
            $client_aggy->persist();
        }
        Alert::success("Trial Membership Types updated.")->flash();

        return Redirect::back();
    }
}
