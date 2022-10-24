<?php

namespace App\Http\Controllers\Data;

use App\Actions\Endusers\Members\UpdateMemberCommunicationPreferences;
use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamDetail;
use App\Enums\LiveReportingEnum;
use App\Http\Controllers\Controller;
use App\Models\LiveReportsByDay;
use App\Models\Note;
use App\Models\ReadReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class MembersController extends Controller
{
    public function index(Request $request)
    {
        $user = request()->user();
        if ($user->cannot('members.read', Member::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $page_count = 10;
        $members = [];
        $members_model = $this->setUpMembersObject($is_client_user, $client_id);
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();
        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $current_team = Team::find($session_team['id']);
        } else {
            $current_team = Team::find(auth()->user()->default_team_id);
        }
        $team_users = $current_team->team_users()->get();
        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }
        if (! empty($members_model)) {
            $members = $members_model
                ->with('location')
//                ->with('membershipType')
//                ->with('detailsDesc')
                ->with('notes')
                ->filter($request->only(
                    'search',
                    'trashed',
                    'createdat',
                    'grlocation',
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
                $sortedResult = $members->getCollection()->sortByDesc($request->get('sort'))->values();
            } else {
                $sortedResult = $members->getCollection()->sortBy($request->get('sort'))->values();
            }
            $members->setCollection($sortedResult);
        }

        if ($user->current_location_id) {
            $newMemberCount = LiveReportsByDay::whereEntity('member')
                ->where('date', '=', date('Y-m-d'))
                ->whereAction(LiveReportingEnum::ADDED)
                ->whereGrLocationId(Location::find($user->current_location_id)->gymrevenue_id)->first();
            if ($newMemberCount) {
                $newMemberCount = $newMemberCount->value;
            } else {
                $newMemberCount = 0;
            }
        } else {
            $newMemberCount = 0;
        }

        $available_member_owners = [];
        foreach ($team_users as $team_user) {
            $available_member_owners[$team_user->user_id] = "{$team_user->user->name}";
        }

        return Inertia::render('Members/Index', [
            'members' => $members,
            'routeName' => request()->route()->getName(),
            'title' => 'Members',
            'owners' => $available_member_owners,
            'locations' => $locations,
            //'isClientUser' => $is_client_user,
            'filters' => $request->all(
                'search',
                'trashed',
                'createdat',
                'grlocation',
                'claimed',
                'date_of_birth',
                'nameSearch',
                'phoneSearch',
                'emailSearch',
                'agreementSearch',
                'lastupdated'
            ),
            'grlocations' => Location::all(),
            'newMemberCount' => $newMemberCount,
        ]);
    }

    public function claimed(Request $request)
    {
        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $prospects = [];

        $locations = Location::all();

        //    $claimed =LeadDetails::whereClientId($client_id)->whereField('claimed')->get();

        if (! empty($prospects_model)) {
            $prospects = $prospects_model
                ->with('location')
//                ->with('membershipType')
//                ->with('detailsDesc')
                //  ->with('leadsclaimed')
                ->filter($request->only('search', 'trashed', 'createdat', 'grlocation'))
                ->orderBy('created_at', 'desc')
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }


        return Inertia::render('Members/Index', [
            'leads' => $prospects,
            'title' => 'Leads',
            //'isClientUser' => $is_client_user,
            'filters' => $request->all('search', 'trashed', 'createdat', 'grlocation'),
            'grlocations' => $locations,
        ]);
    }

    public function create()
    {
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the member to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior
        $user = auth()->user();
        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();

        if ($user->cannot('members.create', Member::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }


        return Inertia::render('Members/Create', [
            'user_id' => $user->id,
            'locations' => $locations,
        ]);
    }

    private function setUpMembersObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];

        if ((! is_null($client_id))) {
            /**
             * BUSINESS RULES
             * 1. There must be an active client and an active team.
             * 2. Client Default Team, then all members from the client
             * 3. Else, get the team_locations for the active_team
             * 4. Query for client id and locations in
             */
            $session_team = session()->get('current_team');
            if ($session_team && array_key_exists('id', $session_team)) {
                $current_team = Team::find($session_team['id']);
            } else {
                $current_team = Team::find(auth()->user()->default_team_id);
            }
            $client = Client::find($client_id);


            $team_locations = [];

            if ($current_team->id != $client->home_team_id) {
                $team_locations_records = TeamDetail::whereTeamId($current_team->id)
                    ->where('field', '=', 'team-location')->get();

                if (count($team_locations_records) > 0) {
                    foreach ($team_locations_records as $team_locations_record) {
                        // @todo - we will probably need to do some user-level scoping
                        // example - if there is scoping and this club is not there, don't include it
                        $team_locations[] = $team_locations_record->value;
                    }

                    $results = Member::whereIn('gr_location_id', $team_locations);
                }
            } else {
                $results = new Member();
            }
        }


        return $results;
    }

    public function edit(Member $member)
    {
        $user = request()->user();
        if ($user->cannot('members.update', Member::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the member to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior
        $user = request()->user();
        $client_id = $user->client_id;
        $is_client_user = $user->isClientUser();
        $locations_records = $this->setUpLocationsObject($is_client_user, $client_id)->get();

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        $member_aggy = MemberAggregate::retrieve($member->id);

        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $current_team = Team::find($session_team['id']);
        } else {
            $current_team = Team::find($user->default_team_id);
        }
        $team_users = $current_team->team_users()->get();
        $member->load('notes');

        //for some reason inertiajs converts "notes" key to empty string.
        //so we set all_notes
        $memberData = $member->toArray();
        $memberData['all_notes'] = $member->notes->toArray();

        foreach ($memberData['all_notes'] as $key => $value) {
            if (ReadReceipt::whereNoteId($memberData['all_notes'][$key]['id'])->first()) {
                $memberData['all_notes'][$key]['read'] = true;
            } else {
                $memberData['all_notes'][$key]['read'] = false;
            }
        }

        return Inertia::render('Members/Edit', [
            'member' => $memberData,
            'user_id' => $user->id,
            'locations' => $locations,
            'interactionCount' => $member_aggy->getInteractionCount(),
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

        if ((! is_null($client_id))) {
            $session_team = session()->get('current_team');
            if ($session_team && array_key_exists('id', $session_team)) {
                $current_team = Team::find($session_team['id']);
            } else {
                $current_team = Team::find(auth()->user()->default_team_id);
            }
            $client = Client::find($client_id);

            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team->id == $client->home_team_id) {
                $results = new Location();
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

    public function contact(Member $member)
    {
        $user = request()->user();
        if ($user->cannot('members.contact', Member::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back()->with('selectedMemberDetailIndex', 0);
        }

        if (array_key_exists('method', request()->all())) {
            $aggy = MemberAggregate::retrieve($member->id);
            $data = request()->all();

            $data['interaction_count'] = 1; // start at one because this action won't be found in stored_events as it hasn't happened yet.
            foreach ($aggy->getAppliedEvents() as $value) {
                $contains = Str::contains(get_class($value), ['MemberWasCalled', 'MemberWasTextMessaged', 'MemberWasEmailed']);
                if ($contains) {
                    $data['interaction_count']++;
                }
            }

            switch (request()->get('method')) {
                    case 'email':
                        $aggy->email($data)->persist();
                        Alert::success("Email sent to member")->flash();

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
        }

        return Redirect::back()->with('selectedMemberDetailIndex', 0);
    }

    public function view(Member $member)
    {
        $user = request()->user();
        if ($user->cannot('members.read', Member::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        $user = request()->user();
        $member_aggy = MemberAggregate::retrieve($member->id);
//        $data = Member::whereId($member_id)->with('detailsDesc')->first();
        $data = Member::whereId($member->id)->first();
        $locid = Location::where('gymrevenue_id', $data->gr_location_id)->first();
        $preview_note = Note::select('note')->whereEntityId($member->id)->get();
        $data = [
            'member' => $member,
            'user_id' => $user->id,
            'club_location' => $locid,
            'interactionCount' => $member_aggy->getInteractionCount(),
            'preview_note' => $preview_note,
        ];

        return $data;
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $user = request()->user();
        if ($user->cannot('members.read', Member::class)) {
            abort(403);
        }

        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $members = [];
        $members_model = $this->setUpMembersObject($is_client_user, $client_id);

        if (! empty($members_model)) {
            $members = $members_model
                ->with('location')
//                ->with('detailsDesc')
                ->filter($request->only(
                    'search',
                    'trashed',
                    'createdat',
                    'grlocation',
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


        return $members;
    }

    public function memberCommunicationPreferences(Request $request, Member $member)
    {
        return view('comms-prefs', ['client' => $member->client, 'entity_type' => 'member', 'entity' => $member]);
    }

    public function updateMemberCommunicationPreferences(Request $request, Member $member)
    {
        $member = UpdateMemberCommunicationPreferences::run($member->id, [
            'email' => $request->subscribe_email === 'on' ? false : true,
            'sms' => $request->subscribe_sms === 'on' ? false : true,
        ]);

        return view('comms-prefs', ['client' => $member->client, 'entity' => $member, 'entity_type' => 'member', 'success' => true]);
    }
}
