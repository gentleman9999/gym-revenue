<?php

namespace App\Http\Controllers\Data;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Endusers\LeadDetails;
use App\Models\Endusers\LeadSource;
use App\Models\Endusers\LeadType;
use App\Models\Endusers\MembershipType;
use App\Models\Endusers\Service;
use App\Models\TeamDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class LeadsController extends Controller
{
    protected $rules = [
        'first_name' => ['required', 'max:50'],
        'last_name' => ['required', 'max:30'],
        'email' => ['required', 'email:rfc,dns'],
        'primary_phone' => ['sometimes'],
        'alternate_phone' => ['sometimes'],
        'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
        'lead_source_id' => ['required', 'exists:lead_sources,id'],
        'lead_type_id' => ['required', 'exists:lead_types,id'],
        'membership_type_id' => ['required', 'exists:membership_types,id'],
        'services' => ['sometimes'],
        'services.*' => ['required', 'exists:services,id'],
//        'user_id' => ['sometimes', 'exists:user,id'],
        'client_id' => 'required',
        'profile_picture' => 'sometimes',
        'profile_picture.uuid' => 'sometimes|required',
        'profile_picture.key' => 'sometimes|required',
        'profile_picture.extension' => 'sometimes|required',
        'profile_picture.bucket' => 'sometimes|required'
    ];

    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $prospects = [];

        $prospects_model = $this->setUpLeadsObject($is_client_user, $client_id);

        if (!empty($prospects_model)) {
            $prospects = $prospects_model
                ->with('location')
                ->with('leadType')
                ->with('membershipType')
                ->with('leadSource')
                ->with('detailsDesc')
                ->filter($request->only('search', 'trashed'))
                ->orderBy('created_at', 'desc')
                ->paginate($page_count);
        }

        return Inertia::render('Leads/Index', [
            'leads' => $prospects,
            'title' => 'Leads',
            //'isClientUser' => $is_client_user,
            'filters' => $request->all('search', 'trashed'),
            'lead_types' => LeadType::whereClientId($client_id)->get()
        ]);
    }

    public function create()
    {
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the lead to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        $lead_types = LeadType::whereClientId($client_id)->get();
        $membership_types = MembershipType::whereClientId($client_id)->get();
        $lead_sources = LeadSource::whereClientId($client_id)->get();
        $available_services = Service::findMany(ClientDetail::whereActive(1)->whereClientId($client_id)->whereDetail('service_id')->pluck('value'));

        return Inertia::render('Leads/Create', [
            'locations' => $locations,
            'lead_types' => $lead_types,
            'membership_types' => $membership_types,
            'lead_sources' => $lead_sources,
            'available_services' => $available_services
        ]);
    }

    public function store(Lead $lead_model)
    {
        $lead_data = request()->validate($this->rules);
//        $lead_data['lead_type_id'] = 1;//manual_create
        $user_id = auth()->user()->id;
//        if (array_key_exists('user_id', $lead_data)) {
//            $user_id = $lead_data['user_id'];
//            unset($lead_data['user_id']);
//        }

        //TODO:all this stuff should happen synchronously via aggregate
        $lead = $lead_model->create($lead_data);

        foreach($lead_data['services'] ?? [] as $service_id){
            LeadDetails::create([
                    'lead_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'field' => 'service_id',
                    'value' => $service_id
                ]
            );
        }

        if(array_key_exists('profile_picture', $lead_data) && $lead_data['profile_picture']){
            $file = $lead_data['profile_picture'];
            $destKey = "{$lead_data['client_id']}/{$file['uuid']}";
            Storage::disk('s3')->move($file['key'], $destKey);
            $file['key'] = $destKey;
            $file['url'] = "https://{$file['bucket']}.s3.amazonaws.com/{$file['key']}";

            LeadDetails::create([
                    'lead_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'field' => 'profile_picture',
                    'misc' => $file
                ]
            );
        }

        Alert::success("Lead '{$lead_data['first_name']} {$lead_data['last_name']}' created")->flash();

        EndUserActivityAggregate::retrieve($lead->id)
            ->manualNewLead($lead->toArray(), $user_id)
            ->claimLead($user_id, $lead_data['client_id'])
            ->persist();

        return Redirect::route('data.leads');
    }

    private function setUpLeadsObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];

        if ((!is_null($client_id))) {
            /**
             * BUSINESS RULES
             * 1. There must be an active client and an active team.
             * 2. Client Default Team, then all leads from the client
             * 3. Else, get the team_locations for the active_team
             * 4. Query for client id and locations in
             */
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();
            $default_team_name = $client->default_team_name->value;
            $team_locations = [];

            if ($current_team->name != $default_team_name) {
                $team_locations_records = TeamDetail::whereTeamId($current_team->id)
                    ->where('name', '=', 'team-location')->get();

                if (count($team_locations_records) > 0) {
                    foreach ($team_locations_records as $team_locations_record) {
                        // @todo - we will probably need to do some user-level scoping
                        // example - if there is scoping and this club is not there, don't include it
                        $team_locations[] = $team_locations_record->value;
                    }

                    $results = Lead::whereClientId($client_id)
                        ->whereIn('gr_location_id', $team_locations);
                }
            } else {
                $results = Lead::whereClientId($client_id);
            }
        }

        return $results;
    }

    public function edit($lead_id)
    {
        // @todo - set up scoping for a sweet Access Denied if this user is not part of the user's scoped access.
        if (!$lead_id) {
            Alert::error("Access Denied or Lead does not exist")->flash();
            return Redirect::route('data.leads');
        }
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the lead to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        $lead_types = LeadType::whereClientId($client_id)->get();
        $membership_types = MembershipType::whereClientId($client_id)->get();
        $lead_sources = LeadSource::whereClientId($client_id)->get();
        $available_services = Service::findMany(ClientDetail::whereActive(1)->whereClientId($client_id)->whereDetail('service_id')->pluck('value'));

        return Inertia::render('Leads/Edit', [
            'lead' => Lead::whereId($lead_id)->with('detailsDesc', 'services', 'profile_picture')->first(),
            'locations' => $locations,
            'lead_types' => $lead_types,
            'membership_types' => $membership_types,
            'lead_sources' => $lead_sources,
            'available_services' => $available_services,
        ]);
    }

    public function show($lead_id)
    {
        // @todo - set up scoping for a sweet Access Denied if this user is not part of the user's scoped access.
        if (!$lead_id) {
            Alert::error("Access Denied or Lead does not exist")->flash();
            return Redirect::route('data.leads');
        }

        return Inertia::render('Leads/Show', [
            'lead' => Lead::whereId($lead_id)->with('detailsDesc')->first()
        ]);
    }

    public function update($lead_id)
    {
        if (!$lead_id) {
            \Alert::info("Access Denied or Lead does not exist")->flash();
            return Redirect::route('data.leads');
        }
        $data = request()->validate($this->rules);

//        $data = request()->all();

	//	dd($data);
        $aggy = EndUserActivityAggregate::retrieve($lead_id)
            ->updateLead($data, auth()->user())
            ->persist();

        Alert::success("Lead '{$data['first_name']} {$data['last_name']}' updated")->flash();


        return Redirect::route('data.leads');
    }

    public function assign()
    {
        $data = request()->all();
        // @todo - change to laravel style Validation

        $claim_detail = LeadDetails::whereLeadId($data['lead_id'])
            ->whereField('claimed')
            ->whereActive(1)
            ->first();

        if (is_null($claim_detail)) {
            LeadDetails::create([
                'client_id' => $data['client_id'],
                'lead_id' => $data['lead_id'],
                'field' => 'claimed',
                'value' => $data['user_id'],
                'misc' => ['claim_date' => date('Y-m-d')]
            ]);

            EndUserActivityAggregate::retrieve($data['lead_id'])
                ->claimLead($data['user_id'], $data['client_id'])
                ->persist();

            \Alert::info('This lead has been claimed by you! You may now interact with it!')->flash();
        } else {
            \Alert::error('This lead has been already been claimed.')->flash();
        }


        return redirect()->back();
    }

    private function setUpLocationsObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];
        /**
         * BUSINESS RULES
         * 1. All Locations
         *  - Cape & Bay user
         *  - The active_team is the current client's default_team (gets all the client's locations)
         * 2. Scoped Locations
         *  - The active_team is not the current client's default_team
         *      so get the teams listed in team_details
         * 3. No Locations
         *  - The active_team is not the current client's default_team
         *      but there are no locations assigned in team_details
         *  - (Bug or Feature?) - The current client is null (cape & bay)
         *      but the user is not a cape & bay user.
         */

        /*$locations = (!is_null($client_id))
            ? Location::whereClientId($client_id)
            : new Location();
        */

        if ((!is_null($client_id))) {
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();
            $default_team_name = $client->default_team_name->value;

            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team->name == $default_team_name) {
                $results = Location::whereClientId($client_id);
            } else {
                // The active_team is not the current client's default_team
                $team_locations = TeamDetail::whereTeamId($current_team->id)
                    ->where('name', '=', 'team-location')->whereActive(1)
                    ->get();

                if (count($team_locations) > 0) {
                    $in_query = [];
                    // so get the teams listed in team_details
                    foreach ($team_locations as $team_location) {
                        $in_query[] = $team_location->value;
                    }

                    $results = Location::whereClientId($client_id)
                        ->whereIn('gymrevenue_id', $in_query);
                }
            }
        } else {
            // Cape & Bay user
            if (!$is_client_user) {
                $results = new Location();
            }
        }

        return $results;
    }

    public function contact($lead_id)
    {
        $lead = Lead::find($lead_id);

        if ($lead) {
            if (array_key_exists('method', request()->all())) {
                $aggy = EndUserActivityAggregate::retrieve($lead_id);
                $data = request()->all();
                switch (request()->get('method')) {
                    case 'email':
                        $aggy->emailLead($data, auth()->user()->id)->persist();
                        Alert::success("Email sent to lead")->flash();
                        break;

                    case 'phone':
                        $aggy->logPhoneCallWithLead($data, auth()->user()->id)->persist();
                        Alert::success("Call Log Updated")->flash();
                        break;

                    case 'sms':
                        $aggy->textMessageLead($data, auth()->user()->id)->persist();
                        Alert::success("SMS Sent")->flash();
                        break;

                    default:
                        Alert::error("Invalid communication method. Select Another.")->flash();
                }
            }
        } else {
            Alert::error("Could not find the lead requested.")->flash();
        }
//        return Redirect::route('data.leads.show', ['id' => $lead_id, 'activeDetailIndex' => 0]);
//        return redirect()->back()->with('selectedLeadDetailIndex', '0');
        return Redirect::back()->with('selectedLeadDetailIndex', 0);
    }




	 public function lead_trash(Request $request,$lead_id)
    {
        if (!$lead_id) {
            Alert::error("No Lead ID provided")->flash();
            return Redirect::back();
        }
         $lead = Lead::whereId($lead_id)->with('detailsDesc')->first();
		 $rmlead = EndUserActivityAggregate::retrieve($lead_id);
        $rmlead->DeleteLead($lead->toArray() , auth()->user()->id)
            ->persist();
			// ->DeleteLead(request()->all(), auth()->user()->id)

        Alert::success("Lead  trashed!")->flash();
      return Redirect::back();

    }



    public function lead_restore(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Lead ID provided")->flash();
            return Redirect::back();
        }
        $lead = Lead::withTrashed()->findOrFail($id);
        $lead->restore();
//dd($lead);
        Alert::success("Lead '{$lead->email}' restored")->flash();

        return Redirect::back();
    }










}
