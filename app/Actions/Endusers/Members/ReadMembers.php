<?php

namespace App\Actions\Endusers\Members;

use App\Models\Clients\Client;
use App\Models\Endusers\Member;
use App\Models\Team;
use App\Models\TeamDetail;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ReadMembers
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
            'per_page' => 'sometimes',
        ];
    }

    public function handle($data, $user = null)
    {
        $page_count = $data['per_page'] > 0 && $data['per_page'] < 1000 ? $data['per_page'] : 10;
        $members = [];
        //$members_model = $this->setUpMembersObject(request()->user()->isClientUser(), request()->user()->currentClientId());
        $members_model = $this->setUpMembersObject(true, '2f108597-fe62-458f-ac30-a159936f7aa');

        if (! empty($members_model)) {
            $members = $members_model
                ->with('location')
                ->with('notes')
                ->orderBy('created_at', 'desc')
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }

        return $members;
    }

    public function asController(ActionRequest $request)
    {
        $members = $this->handle(
            $request->validated(),
            $request->user()
        );

        if ($request->wantsJson()) {
            return $members;
        }
    }

    private function setUpMembersObject(bool $is_client_user, string $client_id = null)
    {
        $results = [];

        if ((! is_null($client_id))) {
            $current_team = Team::whereId(60)->first(); //request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            $default_team_name = $client->default_team_name->value;

            $team_locations = [];

            if ($current_team->id != $default_team_name) {
                $team_locations_records = TeamDetail::whereTeamId($current_team->id)
                    ->where('name', '=', 'team-location')->get();

                if (count($team_locations_records) > 0) {
                    foreach ($team_locations_records as $team_locations_record) {
                        // @todo - we will probably need to do some user-level scoping
                        // example - if there is scoping and this club is not there, don't include it
                        $team_locations[] = $team_locations_record->value;
                    }

                    $results = Member::whereClientId($client_id)
                        ->whereIn('gr_location_id', $team_locations);
                }
            } else {
                $results = Member::whereClientId($client_id);
            }
        }


        return $results;
    }
}
