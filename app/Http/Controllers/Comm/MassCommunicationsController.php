<?php

namespace App\Http\Controllers\Comm;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Endusers\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MassCommunicationsController extends Controller
{
    private function getStats(string $client_id = null)
    {
        $results = [
            'email_templates' => [
                'active' => 0,
                'created' => 0
            ],
            'sms_templates' => [
                'active' => 0,
                'created' => 0
            ],
            'email_campaigns' => [
                'active' => 0,
                'created' => 0
            ],
            'sms_campaigns' => [
                'active' => 0,
                'created' => 0
            ],
            'total_audience' => 0,
            'audience_breakdown' => [
                'all' => 0
            ]
        ];

        if(!is_null($client_id))
        {
            $results['total_audience'] = Lead::whereClientId($client_id)->count();
            $results['audience_breakdown'] = [
                'all' => Lead::whereClientId($client_id)->count(),
                'prospects' => Lead::whereClientId($client_id)->count(),
                'conversions' => 0
            ];
        }
        else
        {
            $results['total_audience'] = 25;
            $results['audience_breakdown'] = [
                'all' => 25,
                'admins' => 10,
                'employees' => 15
            ];
        }

        return $results;
    }

    private function setupTemplatesObject(bool $is_client_user, string $type, string $client_id = null)
    {
        $results = [];

        if((!is_null($client_id)))
        {
            // Get the current client or its cape and bay
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            /**
             * STEPS
             * 1. Get the correct Model
             * 2. Query for all templates with that client id
             * @todo - also add team_id if team_id or null if default team
             * @todo - if the team has scoped clubs, get the query's details for clubs and filter
             * 
             */
        }

        return $results;
    }

    public function index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        if(!is_null($client_id))
        {
            $aud_options = [
                'all' => 'All Audiences',
                'prospects' => 'Prospects',
                'conversions' => 'Conversions'
            ];

            // @todo - make a function that crunches these datas
            $stats = $this->getStats($client_id);
        }
        else
        {
            $aud_options = [
                'all' => 'All Audiences',
                'admins' => 'Cape & Bay Admins',
                'employees' => 'Cape & Bay Non-Admins'
            ];

            $stats = $this->getStats($client_id);
        }

        $active_audience = 'all';
        if(request()->has('audience'))
        {
            $aud = (request()->get('audience'));
            switch($aud)
            {
                case 'all':
                    //@todo - what ever is needed to filfill this need
                    $active_audience = $aud;
                    break;
                case 'prospects':
                case 'conversions':
                case 'admins':
                case 'employees':
                    //@todo - what ever is needed to filfill this need
                    $active_audience = $aud;
                break;

                default:
                    // @todo - this will be a uuid to be looked up
            }
        }

        return Inertia::render('Comms/MassCommsDashboard', [
            'title' => 'Mass Communications',
            'audiences' => $aud_options,
            'activeAudience' => $active_audience,
            'stats' => $stats
        ]);
    }

    public function et_index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $templates = [
            'data' => []
        ];

        $templates_model = $this->setupTemplatesObject($is_client_user, 'email', $client_id);


        return Inertia::render('Comms/Emails/Templates/EmailTemplatesIndex', [
            'title' => 'Email Templates',
            'filters' => request()->all('search', 'trashed'),
            'templates' => $templates,
        ]);
    }

    public function ec_index()
    {
        return Inertia::render('Comms/Emails/Campaigns/EmailCampaignsIndex', [
            'title' => 'Email Campaigns',
            'filters' => request()->all('search', 'trashed')
        ]);
    }

    public function st_index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $templates = [
            'data' => []
        ];

        $templates_model = $this->setupTemplatesObject($is_client_user, 'sms', $client_id);

        return Inertia::render('Comms/SMS/Templates/SMSTemplatesIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed'),
            'templates' => $templates,
        ]);
    }

    public function sc_index()
    {
        return Inertia::render('Comms/SMS/Campaigns/SmsCampaignsIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed')
        ]);
    }
}
