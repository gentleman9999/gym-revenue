<?php

namespace App\Actions\Dashboard\Home;

use App\Models\Clients\Client;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDashboardWidgets
{
    use AsAction;

    public function handle() : array
    {
        $results = [];

        $team = auth()->user()->currentTeam;
        $client_detail = $team->client_details()->first();

        if(!is_null($client_detail))
        {
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
                    'value' => 1,
                    'type' => 'warning',
                    'icon' => 'users'
                ],
                [
                    'title' => 'Active Email Campaigns',
                    'value' => "0",
                    'type' => 'success',
                    'icon' => 'money'
                ],
                [
                    'title' => 'Active SMS Campaigns',
                    'value' => "0",
                    'type' => 'info',
                    'icon' => 'cart'
                ],
                [
                    'title' => "Today's Sales",
                    'value' => "$ 0",
                    'type' => 'danger',
                    'icon' => 'message'
                ],
            ];
        }
        else
        {
            $clients = Client::all();

            $results = [
                [
                    'title' => 'Total Clients',
                    'value' => count($clients),
                    'type' => 'warning',
                    'icon' => 'users'
                ],
                [
                    'title' => 'Total Revenue Funneled',
                    'value' => "$ 0",
                    'type' => 'success',
                    'icon' => 'money'
                ],
                [
                    'title' => 'Total Profits',
                    'value' => "$ 0",
                    'type' => 'info',
                    'icon' => 'cart'
                ],
                [
                    'title' => 'Total MCU Films',
                    'value' => "26",
                    'type' => 'danger',
                    'icon' => 'message'
                ],

            ];
        }

        return $results;
    }
}
