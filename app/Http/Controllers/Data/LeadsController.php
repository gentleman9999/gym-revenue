<?php

namespace App\Http\Controllers\Data;

use App\Domain\Clients\Projections\Client;
use App\Domain\LeadSources\LeadSource;
use App\Domain\LeadStatuses\LeadStatus;
use App\Domain\LeadTypes\LeadType;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamDetail;
use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\Employee;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\Lead;
use App\Enums\LiveReportingEnum;
use App\Http\Controllers\Controller;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\File;
use App\Models\LiveReportsByDay;
use App\Models\Note;
use App\Models\ReadReceipt;
use App\Support\CurrentInfoRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;

class LeadsController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $user = request()->user();
        if ($user->cannot('endusers.read', EndUser::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $page_count = 10;
        $prospects = [];
        $prospects_model = $this->setUpLeadsObject($client_id);
        $opportunities = Lead::select('opportunity')->distinct()->get()->pluck('opportunity');

        if (! empty($prospects_model)) {
            $prospects = $prospects_model
                ->with('location')
                ->with('membershipType')
                //->with('leadSource')
                ->with('notes')
                ->filter($request->only(
                    'search',
                    'trashed',
                    'typeoflead',
                    'createdat',
                    'grlocation',
                    //'leadsource',
                    'claimed',
                    'opportunity',
                    'claimed',
                    'date_of_birth',
                    'nameSearch',
                    'phoneSearch',
                    'emailSearch',
                    'agreementSearch',
                    'lastupdated'
                ))
                ->orderBy('created_at', 'desc')
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }

        //THIS DOESN'T WORK BECAUSE OF PAGINATION BUT IT MAKES IT LOOK LIKE IT'S WORKING FOR NOW
        //MUST FIX BY DEMO 6/15/22
        //THIS BLOCK HAS TO BE REMOVED & QUERIES REWRITTEN WITH JOINS SO ACTUAL SORTING WORKS WITH PAGINATION
        if ($request->get('sort') != '') {
            if ($request->get('dir') == 'DESC') {
                $sortedResult = $prospects->getCollection()->sortByDesc($request->get('sort'))->values();
            } else {
                $sortedResult = $prospects->getCollection()->sortBy($request->get('sort'))->values();
            }
            $prospects->setCollection($sortedResult);
        }

        $current_team = CurrentInfoRetriever::getCurrentTeam();
        $x = ['name' => null, 'id' => null];
        $team_users = Employee::whereIn('id', $current_team->team_users()->get()->pluck('user_id')->toArray());
        $available_lead_owners = [];
        foreach ($team_users as $team_user) {
            $available_lead_owners[] = [
                'name' => $team_user->user->name,
                "id" => $team_user->user->id,
            ];
        }

        if (CurrentInfoRetriever::getCurrentLocationID()) {
            $newLeadCount = LiveReportsByDay::whereEntity('lead')
                ->where('date', '=', date('Y-m-d'))
                ->whereAction(LiveReportingEnum::ADDED)
                ->whereGrLocationId(Location::find(CurrentInfoRetriever::getCurrentLocationID())->gymrevenue_id)->first();

            if ($newLeadCount) {
                $newLeadCount = $newLeadCount->value;
            } else {
                $newLeadCount = 0;
            }
        } else {
            $newLeadCount = 0;
        }

        return Inertia::render('Leads/Index', [
            'leads' => $prospects,
            'routeName' => request()->route()->getName(),
            'title' => 'Leads',
            //'isClientUser' => $is_client_user,
            'filters' => $request->all(
                'search',
                'trashed',
                'typeoflead',
                'createdat',
                'grlocation',
                'leadsource',
                'claimed',
                'opportunity',
                'claimed',
                'date_of_birth',
                'nameSearch',
                'phoneSearch',
                'emailSearch',
                'agreementSearch',
                'lastupdated'
            ),
            'owners' => $available_lead_owners,
            'grlocations' => Location::all(),
            'leadsources' => LeadSource::all(),
            'opportunities' => array_values($opportunities->toArray()),
            'newLeadCount' => $newLeadCount,
            'lead_types' => LeadType::all(),
        ]);
    }

    public function claimed(Request $request)
    {
        $client_id = request()->user()->currentClientId();

        $page_count = 10;
        $prospects = [];

        $prospects_model = $this->setUpLeadsObjectclaimed($client_id);

        $locations = Location::all();
        $leadsource = LeadSource::all();

        if (! empty($prospects_model)) {
            $prospects = $prospects_model
                ->with('location')
                ->with('leadType')
                ->with('membershipType')
                ->with('leadSource')
                ->with('detailsDesc')
                ->filter($request->only('search', 'trashed', 'typeoflead', 'createdat', 'grlocation', 'leadsource'))
                ->orderBy('created_at', 'desc')
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }

        return Inertia::render('Leads/Index', [
            'leads' => $prospects,
            'title' => 'Leads',
            //'isClientUser' => $is_client_user,
            'filters' => $request->all('search', 'trashed', 'typeoflead', 'createdat', 'grlocation', 'leadsource'),
            'lead_types' => LeadType::all(),
            'grlocations' => $locations,
            'leadsources' => $leadsource,

        ]);
    }

    public function create()
    {
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the lead to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior
        $user = auth()->user();
        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();
        $current_team = CurrentInfoRetriever::getCurrentTeam();
        $team_users = $current_team->team_users()->get();


        if ($user->cannot('endusers.create', EndUser::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        //$lead_types = LeadType::all();
        $lead_sources = LeadSource::all();
        //$lead_statuses = LeadStatus::all();


        /**
         * STEPS for team users
         * 1. No CnB Admins unless it is you
         * 2. Unless Cnb Admin, no admin users
         */
        $available_lead_owners = [];
        foreach ($team_users as $team_user) {
            $available_lead_owners[$team_user->user_id] = "{$team_user->user->name}";
        }

        return Inertia::render('Leads/Create', [
            'user_id' => $user->id,
            'locations' => $locations,
            //'lead_types' => $lead_types,
            'lead_sources' => $lead_sources,
            //'lead_statuses' => $lead_statuses,
            'lead_owners' => $available_lead_owners,
        ]);
    }

    private function setUpLeadsObject(string $client_id = null)
    {
        $results = [];

        if ((! is_null($client_id))) {
            /**
             * BUSINESS RULES
             * 1. There must be an active client and an active team.
             * 2. Client Default Team, then all leads from the client
             * 3. Else, get the team_locations for the active_team
             * 4. Query for client id and locations in
             */
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client = Client::find($client_id);


            $team_locations = [];

            if ($current_team->id != $client->home_team_id) {
                $team_locations = TeamDetail::whereTeamId($current_team->id)
                    ->where('field', '=', 'team-location')->get()->pluck('value')->toArray();
                // @todo - we will probably need to do some user-level scoping
                // example - if there is scoping and this club is not there, don't include it

                $results = Lead::whereIn('home_location_id', $team_locations);
            } else {
                $results = new Lead();
            }
        }


        return $results;
    }

    private function setUpLeadsObjectclaimed(string $client_id = null)
    {
        $results = [];

        if ((! is_null($client_id))) {
            /**
             * BUSINESS RULES
             * 1. There must be an active client and an active team.
             * 2. Client Default Team, then all leads from the client
             * 3. Else, get the team_locations for the active_team
             * 4. Query for client id and locations in
             */
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client = Client::find($client_id);
            $team_locations = [];

            if ($current_team->name != $client->home_team_id) {
                $team_locations_records = TeamDetail::whereTeamId($current_team->id)
                    ->where('name', '=', 'team-location')->get();

                if (count($team_locations_records) > 0) {
                    foreach ($team_locations_records as $team_locations_record) {
                        // @todo - we will probably need to do some user-level scoping
                        // example - if there is scoping and this club is not there, don't include it
                        $team_locations[] = $team_locations_record->value;
                    }
                    $results = Lead::whereIn('home_location_id', $team_locations)->whereHas('claimed');
                }
            } else {
                $results = Lead::whereHas('claimed');
            }
        }

        return $results;
    }

    public function edit(Lead $endUser): InertiaResponse
    {
        $user = request()->user();
        if ($user->cannot('endusers.update', EndUser::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the lead to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior
        $user = request()->user();
        $client_id = $user->client_id;
        $is_client_user = $user->isClientUser();
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        $current_team = CurrentInfoRetriever::getCurrentTeam();
        $team_users = $current_team->team_users()->get();
        /**
         * STEPS for team users
         * 1. No CnB Admins unless it is you
         * 2. Unless Cnb Admin, no admin users
         */
        $available_lead_owners = [];
        foreach ($team_users as $team_user) {
            $available_lead_owners[$team_user->user_id] = "{$team_user->user->name}";
        }

        //for some reason inertiajs converts "notes" key to empty string.
        //so we set all_notes
        $lead_data = $endUser->toArray();
        $lead_data['all_notes'] = $endUser->notes->toArray();

        // if ($lead_data['profile_picture_file_id']) {
        //     $lead_data['profile_picture'] = File::whereId($lead_data['profile_picture_file_id'])->first();
        // }

        foreach ($lead_data['all_notes'] as $key => $value) {
            if (ReadReceipt::whereNoteId($lead_data['all_notes'][$key]['id'])->first()) {
                $lead_data['all_notes'][$key]['read'] = true;
            } else {
                $lead_data['all_notes'][$key]['read'] = false;
            }
        }

        return Inertia::render('Leads/Edit', [
            'lead' => $lead_data,
            'user_id' => $user->id,
            'locations' => $locations,
            'lead_owners' => $available_lead_owners,
        ]);
    }

    public function show(Lead $endUser): InertiaResponse
    {
        $aggy = UserAggregate::retrieve($endUser->id);
        $preview_note = Note::select('note')->whereEntityId($endUser->id)->get();


        return Inertia::render('Leads/Show', [
            'lead' => $endUser,
            'preview_note' => $preview_note,
            'interactionCount' => $aggy->getInteractionCount(),
            'trialMembershipTypes' => TrialMembershipType::whereClientId(request()->user()->client_id)->get(),
            'hasTwilioConversation' => $endUser->client->hasTwilioConversationEnabled(),
        ]);
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

        if ((! is_null($client_id))) {
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client = Client::find($client_id);

            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team->id == $client->home_team_id) {
                $results = Location::whereClientId($client_id);
            } else {
                // The active_team is not the current client's default_team
                $team_locations = TeamDetail::whereTeamId($current_team->id)
                    ->where('field', '=', 'team-location')
                    ->get();

                if (count($team_locations) > 0) {
                    $in_query = [];
                    // so get the teams listed in team_details
                    foreach ($team_locations as $team_location) {
                        $in_query[] = $team_location->value;
                    }

                    $results = Location::whereIn('gymrevenue_id', $in_query);
                }
            }
        } else {
            // Cape & Bay user
            if (! $is_client_user) {
                $results = new Location();
            }
        }

        return $results;
    }

    public function contact(Lead $end_user): \Illuminate\Http\RedirectResponse
    {
        $user = request()->user();
        if ($user->cannot('endusers.contact', EndUser::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back()->with('selectedLeadDetailIndex', 0);
        }


        if (array_key_exists('method', request()->all())) {
            $aggy = UserAggregate::retrieve($end_user->id);
            $data = request()->all();

            $data['interaction_count'] = 1; // start at one because this action won't be found in stored_events as it hasn't happened yet.
            foreach ($aggy->getAppliedEvents() as $value) {
                $contains = Str::contains(get_class($value), ['LeadWasCalled', 'LeadWasTextMessaged', 'LeadWasEmailed']);
                if ($contains) {
                    $data['interaction_count']++;
                }
            }

            $data['user'] = auth()->user()->id;
            // Remove following If statement prior to going live
            if ($end_user->isCBorGR()) {
                switch (request()->get('method')) {
                    case 'email':
                        $aggy->email($data)->persist();
                        Alert::success("Email sent to lead")->flash();

                        break;

                    case 'phone':
                        $aggy->logPhoneCall($data)->persist();
                        Alert::success("Call Log Updated")->flash();

                        break;

                    case 'sms':
                        $aggy->textMessage($data)->persist();
                        Alert::success("SMS Sent")->flash();

                        break;

                    default:
                        Alert::error("Invalid communication method. Select Another.")->flash();
                }
            } else {
                Alert::error("Lead does not have a Cape and Bay or Gym Revenue email address.")->flash();
            }
        }

//        return Redirect::route('data.leads.show', ['id' => $lead_id, 'activeDetailIndex' => 0]);
//        return redirect()->back()->with('selectedLeadDetailIndex', '0');
        return Redirect::back()->with('selectedLeadDetailIndex', 0);
    }

    public function sources(Request $request)
    {
        return Inertia::render('Leads/Sources', [
            'sources' => LeadSource::get(['id', 'name']),
        ]);
    }

    public function statuses(Request $request)
    {
        return Inertia::render('Leads/Statuses', [
        //    'statuses' => LeadStatus::get(['id', 'status']),
        ]);
    }

    public function view(Lead $endUser): array
    {
        $user = request()->user();
        if ($user->cannot('endusers.read', EndUser::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        $lead_aggy = UserAggregate::retrieve($endUser->id);
        $locid = Location::where('gymrevenue_id', $endUser->home_location_id)->first();
        $preview_note = Note::select('note')->whereEntityId($endUser->id)->get();

        return [
            'lead' => $endUser,
            'user_id' => $user->id,
            'club_location' => $locid,
            'interactionCount' => $lead_aggy->getInteractionCount(),
            'preview_note' => $preview_note,
        ];
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $user = request()->user();
        if ($user->cannot('endusers.read', EndUser::class)) {
            abort(403);
        }

        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $prospects = [];
        $prospects_model = $this->setUpLeadsObject($client_id);

        if (! empty($prospects_model)) {
            $prospects = $prospects_model
                ->with('location')
                ->with('leadType')
                ->with('membershipType')
                ->with('leadSource')
                ->with('claimed')
                ->with('detailsDesc')
                ->filter($request->only(
                    'search',
                    'trashed',
                    'typeoflead',
                    'createdat',
                    'grlocation',
                    'leadsource',
                    'opportunity',
                    'claimed',
                    'date_of_birth',
                    'nameSearch',
                    'phoneSearch',
                    'emailSearch',
                    'agreementSearch',
                    'lastupdated'
                ))
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $prospects;
    }
}
