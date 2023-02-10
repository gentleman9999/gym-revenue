<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Enums\SocialMediaEnum;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\Domain\EntrySources\EntrySource;
use App\Enums\ClientServiceEnum;
use App\Models\ClientCommunicationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;

class ClientSettingsController extends Controller
{
    public function index(): InertiaResponse|RedirectResponse
    {
        $client_id = ($user = request()->user())->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        if ($user->cannot('manage-client-settings')) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        $client = Client::with(['trial_membership_types', 'locations'])->find($client_id);

        return Inertia::render('ClientSettings/Index', [
            'availableServices' => collect(ClientServiceEnum::cases())->keyBy('name')->values()->map(
                fn ($s) => ['value' => $s->value, 'name' => $s->name]
            ),
            'commPreferences' => ClientCommunicationPreference::first(),
            'availableCommPreferences' => ClientCommunicationPreference::COMMUNICATION_TYPES,
            'services' => $client->services ?? [],
            'trialMembershipTypes' => $client->trial_membership_types ?? [],
            'entrySources' => EntrySource::all(),
            'entrySourceCategories' => EntrySourceCategory::all(),
            'locations' => $client->locations ?? [],
            'socialMedias' => $client->getSocialMedia(),
            'availableSocialMedias' => collect(SocialMediaEnum::cases())->map(
                fn (SocialMediaEnum $enum) => ['name' => $enum->name, 'value' => $enum->value]
            ),
            'gateways' => ClientGatewaySetting::all(),
            'logoUrl' => Client::findOrFail($client_id)->logo_url(),
        ]);
    }

    //TODO:update to action
    public function updateTrialMembershipTypes(Request $request): RedirectResponse
    {
        $data = request()->validate([
            'trialMembershipTypes' => ['sometimes', 'array'],
            'trialMembershipTypes.*.type_name' => ['required'],
            'trialMembershipTypes.*.slug' => ['required'],
            'trialMembershipTypes.*.trial_length' => ['required'],
        ]);
        if (array_key_exists('trialMembershipTypes', $data) && is_array($data['trialMembershipTypes'])) {
            $trial_membership_types_to_update = collect($data['trialMembershipTypes'])->filter(function ($t) {
                return $t['id'] !== null;
            });
            $trial_membership_type_to_create  = collect($data['trialMembershipTypes'])->filter(function ($t) {
                return $t['id'] === null;
            });

            $client_aggy = ClientAggregate::retrieve($request->user()->client_id);

            foreach ($trial_membership_types_to_update as $trial_membership_type_data) {
                $client_aggy->updateTrialMembershipType($trial_membership_type_data, request()->user()->id);
            }
            foreach ($trial_membership_type_to_create as $trial_membership_type_data) {
                $client_aggy->createTrialMembershipType($trial_membership_type_data, request()->user()->id);
            }
            $client_aggy->persist();
        }
        Alert::success("Trial Membership Types updated.")->flash();

        return Redirect::back();
    }
}
