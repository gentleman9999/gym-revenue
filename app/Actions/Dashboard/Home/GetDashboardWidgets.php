<?php

namespace App\Actions\Dashboard\Home;

use App\Domain\Clients\Models\Client;
use App\Domain\Teams\Models\TeamDetail;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Clients\Location;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDashboardWidgets
{
    use AsAction;

    public function handle(): array
    {
        $results = [];

        $team = auth()->user()->currentTeam;

        if (! is_null($team->client)) {
            $num_locs = 0;
            $num_ecs = 0;
            $num_scs = 0;
            $last_widget_count = 0;
            if ($team->home_team) {
                $num_locs = Location::whereClientId($team->client_id)->whereActive(1)->count();
                $num_ecs = EmailCampaigns::whereClientId($team->client_id)->count();
                $num_scs = SmsCampaigns::whereClientId($team->client_id)->count();
            } else {
                // get the locations the active team has access to
                $num_locs = TeamDetail::whereTeamId($team->id)
                    ->where('name', '=', 'team-location')->whereActive(1)
                    ->count();

                // @todo - these queries need to be adjusted for all "any" campaigns and campaigns scoped to their team
                // @todo - maybe add a filter() function to the models.
                if (true) { // Check that the user is not a sales rep
                    $num_ecs = EmailCampaigns::whereClientId($team->client_id)->count();
                    $num_scs = SmsCampaigns::whereClientId($team->client_id)->count();
                }
                // @todo - an else
            }

            /** @todo - find the context
             * 1. Default Team shows
             *      -- Total Locations
             *      -- Active Email Campaigns
             *      -- Active SMS Campaigns
             *      -- Today's Sales
             *
             * 2. Team Home
             *      -- Today's Leads
             *      -- Total Leads
             *      -- Total Team Members
             *      -- Total Locations
             *
             * 3. Team Sales Rep
             *      -- Today's Hot New Leads
             *      -- Total Leads
             *      -- Leads Assigned to You
             *      -- Leads Needing Your Attention
             */
            $results = [
                [
                    'title' => 'Total Locations',
                    'value' => $num_locs,
                    'type' => 'warning',
                    'icon' => 'users',
                ],
                [
                    'title' => 'Active Email Campaigns',
                    'value' => $num_ecs,
                    'type' => 'success',
                    'icon' => 'money',
                ],
                [
                    'title' => 'Active SMS Campaigns',
                    'value' => $num_scs,
                    'type' => 'info',
                    'icon' => 'cart',
                ],
                [
                    'title' => "Today's Sales",
                    'value' => "$ 0",
                    'type' => 'danger',
                    'icon' => 'message',
                ],
            ];
        } else {
            $clients = Client::all();

            $results = [
                [
                    'title' => 'Total Clients',
                    'value' => count($clients),
                    'type' => 'warning',
                    'icon' => 'users',
                ],
                [
                    'title' => 'Total Revenue Funneled',
                    'value' => "$ 0",
                    'type' => 'success',
                    'icon' => 'money',
                ],
                [
                    'title' => 'Total Profits',
                    'value' => "$ 0",
                    'type' => 'info',
                    'icon' => 'cart',
                ],
                [
                    'title' => 'Total MCU Films',
                    'value' => "28",
                    'type' => 'danger',
                    'icon' => 'message',
                ],

            ];
        }

        return $results;
    }
}
