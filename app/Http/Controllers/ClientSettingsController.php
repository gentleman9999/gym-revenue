<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Enums\SocialMediaEnum;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Projections\Client;
use App\Enums\ClientServiceEnum;
use App\Models\ClientCommunicationPreference;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class ClientSettingsController extends Controller
{
    public function index()
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }

        if (request()->user()->cannot('manage-client-settings')) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        $client = Client::with(['trial_membership_types', 'locations'])->find($client_id);

        return Inertia::render('ClientSettings/Index', [
            'availableServices' => collect(ClientServiceEnum::cases())->keyBy('name')->values()->map(function ($s) {
                return ['value' => $s->value, 'name' => $s->name];
            }),
            'commPreferences' => ClientCommunicationPreference::first(),
            'availableCommPreferences' => ClientCommunicationPreference::COMMUNICATION_TYPES,
            'services' => $client->services ?? [],
            'trialMembershipTypes' => $client->trial_membership_types ?? [],
            'locations' => $client->locations ?? [],
//            'socialMedias' => ClientSocialMedia::all(),
            'socialMedias' => $client->getSocialMedia(),
            'availableSocialMedias' => collect(SocialMediaEnum::cases())->map(fn (SocialMediaEnum $enum) => ['name' => $enum->name, 'value' => $enum->value]),
            'gateways' => ClientGatewaySetting::all(),
            'logoUrl' => Client::findOrFail($client_id)->logo_url(),
        ]);
    }

    //TODO:update to action
    public function updateTrialMembershipTypes()
    {
        $data = request()->validate([
            'trialMembershipTypes' => ['sometimes', 'array'],
            'trialMembershipTypes.*.type_name' => ['required'],
            'trialMembershipTypes.*.slug' => ['required'],
            'trialMembershipTypes.*.trial_length' => ['required'],
        ]);
        if (array_key_exists('trialMembershipTypes', $data) && is_array($data['trialMembershipTypes'])) {
            $trialMembershipTypesToUpdate = collect($data['trialMembershipTypes'])->filter(function ($t) {
                return $t['id'] !== null;
            });
            $trialMembershipTypesToCreate = collect($data['trialMembershipTypes'])->filter(function ($t) {
                return $t['id'] === null;
            });

            $client_aggy = ClientAggregate::retrieve($request->user()->client_id);

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
