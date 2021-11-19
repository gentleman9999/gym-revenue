<?php

namespace App\Http\Controllers\Comm;

use App\Http\Controllers\Controller;
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
        return Inertia::render('Comms/Emails/Templates/EmailTemplatesIndex', [
            'title' => 'Email Templates',
        ]);
    }

    public function ec_index()
    {
        return Inertia::render('Comms/Emails/Campaigns/EmailCampaignsIndex', [
            'title' => 'Email Campaigns',
        ]);
    }

    public function st_index()
    {
        return Inertia::render('Comms/SMS/Templates/SMSTemplatesIndex', [
            'title' => 'SMS Templates',
        ]);
    }

    public function sc_index()
    {
        return Inertia::render('Comms/SMS/Campaigns/SmsCampaignsIndex', [
            'title' => 'SMS Templates',
        ]);
    }
}
