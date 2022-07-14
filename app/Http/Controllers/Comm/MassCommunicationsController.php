<?php

namespace App\Http\Controllers\Comm;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Campaigns\DripCampaigns\DripCampaign;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Clients\Projections\ClientActivity;
use App\Domain\Leads\Models\Lead;
use App\Http\Controllers\Controller;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MassCommunicationsController extends Controller
{
    private function getStats(string $client_id = null)
    {
        $results = [
            'email_templates' => [
                'active' => 0,
                'created' => 0,
            ],
            'sms_templates' => [
                'active' => 0,
                'created' => 0,
            ],
            'drip_campaigns' => [
                'active' => 0,
                'created' => 0,
            ],
            'scheduled_campaigns' => [
                'active' => 0,
                'created' => 0,
            ],
            'total_audience' => 0,
            'audience_breakdown' => [
                'all' => 0,
            ],
        ];

        if (! is_null($client_id)) {
            $results['total_audience'] = Lead::whereClientId($client_id)->count();
            $results['audience_breakdown'] = [
                'all' => Lead::whereClientId($client_id)->count(),
                'prospects' => Lead::whereClientId($client_id)->count(),
                'conversions' => 0,
            ];
            $results['sms_templates'] = [
                'created' => SmsTemplates::whereClientId($client_id)->count(),
                'active' => SmsTemplates::whereClientId($client_id)->whereActive(1)->count(),
            ];
            $results['scheduled_campaigns'] = [
                'created' => ScheduledCampaign::count(),
                'active' => ScheduledCampaign::where('status', '!=', 'draft')->count(),
            ];
            $results['email_templates'] = [
                'created' => EmailTemplates::whereClientId($client_id)->count(),
                'active' => EmailTemplates::whereClientId($client_id)->whereActive(1)->count(),
            ];
            $results['drip_campaigns'] = [
                'created' => DripCampaign::count(),
                'active' => DripCampaign::where('status', '!=', 'draft')->count(),
            ];
        } else {
            $results['total_audience'] = 25;
            $results['audience_breakdown'] = [
                'all' => 25,
                'admins' => 10,
                'employees' => 15,
            ];
        }

        return $results;
    }

    private function filterHistoryLog(array $history_log, string $audience, string $client_id = null)
    {
        $results = [];
        //dd($audience, $history_log);
        // look for logs about the audience
        foreach ($history_log as $idx => $log) {
            if (array_key_exists('slug', $log) && ($log['slug'] == $audience)) {
                $results[] = $log;
            }
        }
        //dd($results);

        return $results;
    }

    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        //
//            $aggy = ClientAggregate::retrieve($client_id);
//            $history_log = $aggy->getCommunicationHistoryLog();
//            $aud_options = [
//                'all' => 'All',
//                'prospects' => 'Prospects',
//                'conversions' => 'Conversions',
//            ];
//
//            // @todo - make a function that crunches these datas
//            $stats = $this->getStats($client_id);

        $history_log = [];
        $aud_options = [
                'all' => 'All',
                'prospects' => 'Prospects',
                'conversions' => 'Conversions',
            ];

        $stats = $this->getStats($client_id);


        $active_audience = 'all';
        if (request()->has('audience')) {
            $aud = (request()->get('audience'));
            switch ($aud) {
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
                    $history_log = $this->filterHistoryLog($history_log, $aud, $client_id);

                    break;

                default:
                    // @todo - this will be a uuid to be looked up
            }
        }

        //search filter
        $search = $request->query('search');
        $history_log = collect($history_log)->filter(function ($value, $key) use ($search) {
            if (str_contains(strtolower($value['type']), strtolower($search))) {
                return true;
            } elseif (str_contains(strtolower($value['recordName']), strtolower($search))) {
                return true;
            } elseif (str_contains(strtolower($value['date']), strtolower($search))) {
                return true;
            } elseif (str_contains(strtolower($value['by']), strtolower($search))) {
                return true;
            }

            return false;
        });

        $comms_history = ClientActivity::with('user')->whereIn('entity', [ScheduledCampaign::class, DripCampaign::class])->paginate(10);

        return Inertia::render('Comms/MassCommsDashboard', [
            'title' => 'Mass Communications',
            'audiences' => $aud_options,
            'activeAudience' => $active_audience,
            'stats' => $stats,
            'historyFeed' => $comms_history,
//            'historyFeed' => $history_log
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        if (! is_null($client_id)) {
            $aggy = ClientAggregate::retrieve($client_id);
            $history_log = $aggy->getCommunicationHistoryLog();
            $aud_options = [
                'all' => 'All',
                'prospects' => 'Prospects',
                'conversions' => 'Conversions',
            ];

            // @todo - make a function that crunches these datas
            $stats = $this->getStats($client_id);
        } else {
            $history_log = [];
            $aud_options = [
                'all' => 'All',
                'admins' => 'Cape & Bay Admins',
                'employees' => 'Cape & Bay Non-Admins',
            ];

            $stats = $this->getStats($client_id);
        }

        $active_audience = 'all';
        if (request()->has('audience')) {
            $aud = (request()->get('audience'));
            switch ($aud) {
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
                    $history_log = $this->filterHistoryLog($history_log, $aud, $client_id);

                    break;

                default:
                    // @todo - this will be a uuid to be looked up
            }
        }

        //search filter
        $search = $request->query('search');
        $history_log = collect($history_log)->filter(function ($value, $key) use ($search) {
            if (str_contains(strtolower($value['type']), strtolower($search))) {
                return true;
            } elseif (str_contains(strtolower($value['recordName']), strtolower($search))) {
                return true;
            } elseif (str_contains(strtolower($value['date']), strtolower($search))) {
                return true;
            } elseif (str_contains(strtolower($value['by']), strtolower($search))) {
                return true;
            }

            return false;
        });

        return $history_log;
    }
}
