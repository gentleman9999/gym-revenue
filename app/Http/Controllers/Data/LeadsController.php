<?php

namespace App\Http\Controllers\Data;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Endusers\Lead;
use App\Models\TeamDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $prospects = [];

        $prospects_model = $this->setUpLeadsObject($is_client_user, $client_id);

        if(!empty($prospects_model))
        {
            $prospects = $prospects_model->with('location')->with('detailsDesc')
                ->filter($request->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Leads/Index', [
            'leads' => $prospects,
            'title' => 'Leads',
            //'isClientUser' => $is_client_user,
            'filters' => $request->all('search', 'trashed')
        ]);
    }

    private function setUpLeadsObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];

        if((!is_null($client_id)))
        {
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

            if($current_team->name != $default_team_name)
            {
                $team_locations_records = TeamDetail::whereTeamId($current_team->id)
                    ->where('name', '=', 'team-location')->get();

                if(count($team_locations_records) > 0)
                {
                    foreach ($team_locations_records as $team_locations_record)
                    {
                        // @todo - we will probably need to do some user-level scoping
                        // example - if there is scoping and this club is not there, don't include it
                        $team_locations[] = $team_locations_record->value;
                    }

                    $results = Lead::whereClientId($client_id)
                        ->whereIn('gr_location_id', $team_locations);
                }
            }
            else
            {
                $results = Lead::whereClientId($client_id);
            }
        }

        return $results;
    }

    public function edit($lead_id)
    {
        // @todo - set up scoping for a sweet Access Denied if this user is not part of the user's scoped access.
        if (!$lead_id) {
            //TODO:flash error
            \Alert::info("Access Denied or Lead does not exist")->flash();
            return Redirect::route('data.leads');
        }

        return Inertia::render('Leads/Edit', [
            'lead' => Lead::whereId($lead_id)->with('detailsDesc')->first(),
        ]);
    }

    public function show($lead_id)
    {
        // @todo - set up scoping for a sweet Access Denied if this user is not part of the user's scoped access.
        if (!$lead_id) {
            //TODO:flash error
            \Alert::info("Access Denied or Lead does not exist")->flash();
            return Redirect::route('data.leads');
        }

        return Inertia::render('Leads/Show', [
            'lead' => Lead::whereId($lead_id)->with('detailsDesc')->first(),
        ]);
    }

    public function update($lead_id)
    {
        if (!$lead_id) {
            \Alert::info("Access Denied or Lead does not exist")->flash();
            return Redirect::route('data.leads');
        }
        $data = request()->all();

        $aggy = EndUserActivityAggregate::retrieve($lead_id)
            ->updateLead($data, auth()->user())
            ->persist();

        return Redirect::route('data.leads');
    }
}
