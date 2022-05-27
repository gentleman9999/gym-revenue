<?php

namespace App\Actions\Endusers\Leads;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\TeamDetail;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadLead
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }

    public function handle($data, $current_user = null)
    {
        $page_count = 10;
        $prospects = [];
        $prospects_model = $this->setUpLeadsObject(request()->user()->isClientUser(), request()->user()->currentClientId());

        if (! empty($prospects_model)) {
            $prospects = $prospects_model
                ->with('location')
                ->with('leadType')
                ->with('membershipType')
                ->with('leadSource')
                ->with('leadsclaimed')
                ->with('detailsDesc')
                ->with('opportunity')
                ->with('notes')
                ->orderBy('created_at', 'desc')
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }

        return $prospects;
    }

    public function asController(ActionRequest $request)
    {
        $lead = $this->handle(
            $request->validated(),
            $request->user(),
        );

        if (! $request->headers->has('X-inertia')) {
            return $lead;
        }
    }

    private function setUpLocationsObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];

        if ((! is_null($client_id))) {
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();
            $default_team_name = $client->default_team_name->value;

            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team->id == $default_team_name) {
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
            if (! $is_client_user) {
                $results = new Location();
            }
        }

        return $results;
    }
}
