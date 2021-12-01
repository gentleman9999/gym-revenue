<?php

namespace App\Http\Controllers\Comm;

use App\Aggregates\Clients\ClientAggregate;
use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\Models\Endusers\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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

        if (!is_null($client_id)) {
            $results['total_audience'] = Lead::whereClientId($client_id)->count();
            $results['audience_breakdown'] = [
                'all' => Lead::whereClientId($client_id)->count(),
                'prospects' => Lead::whereClientId($client_id)->count(),
                'conversions' => 0
            ];
        } else {
            $results['total_audience'] = 25;
            $results['audience_breakdown'] = [
                'all' => 25,
                'admins' => 10,
                'employees' => 15
            ];
        }

        return $results;
    }

    private function setupCampaignsObject(bool $is_client_user, string $type, string $client_id = null)
    {
        $results = [];

        if ((!is_null($client_id))) {
            // Get the current client or its cape and bay
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            // Get the correct Model
            $template_model = ($type == 'email') ? new EmailCampaigns() : new SmsCampaigns();
            // Query for all templates with that client id
            $template_model = $template_model->whereClientId($client_id);

            /**
             * STEPS
             * 2.
             * @todo - also add team_id if team_id or null if default team
             * @todo - if the team has scoped clubs, get the query's details for clubs and filter
             *
             */
            $results = $template_model;
        } else {
            if (!$is_client_user) {
                $template_model = ($type == 'email') ? new EmailCampaigns() : new SmsCampaigns();
                $template_model = $template_model->whereNull('client_id');
                /**
                 * STEPS
                 * 2.
                 * @todo - also add team_id if team_id or null if default team
                 * @todo - if the team has scoped clubs, get the query's details for clubs and filter
                 *
                 */
                $results = $template_model;
            }
        }

        return $results;
    }

    private function setupTemplatesObject(bool $is_client_user, string $type, string $client_id = null)
    {
        $results = [];

        if ((!is_null($client_id))) {
            // Get the current client or its cape and bay
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            // Get the correct Model
            $template_model = ($type == 'email') ? new EmailTemplates() : new SmsTemplates();
            // Query for all templates with that client id
            $template_model = $template_model->whereClientId($client_id);

            /**
             * STEPS
             * 2.
             * @todo - also add team_id if team_id or null if default team
             * @todo - if the team has scoped clubs, get the query's details for clubs and filter
             *
             */
            $results = $template_model;
        } else {
            if (!$is_client_user) {
                $template_model = ($type == 'email') ? new EmailTemplates() : new SmsTemplates();
                $template_model = $template_model->whereNull('client_id');
                /**
                 * STEPS
                 * 2.
                 * @todo - also add team_id if team_id or null if default team
                 * @todo - if the team has scoped clubs, get the query's details for clubs and filter
                 *
                 */
                $results = $template_model;
            }
        }


        return $results;
    }

    public function index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        if (!is_null($client_id)) {
            $aggy = ClientAggregate::retrieve($client_id);
            $history_log = $aggy->getCommunicationHistoryLog();
            $aud_options = [
                'all' => 'All Audiences',
                'prospects' => 'Prospects',
                'conversions' => 'Conversions',
            ];

            // @todo - make a function that crunches these datas
            $stats = $this->getStats($client_id);
        } else {
            $history_log = [];
            $aud_options = [
                'all' => 'All Audiences',
                'admins' => 'Cape & Bay Admins',
                'employees' => 'Cape & Bay Non-Admins'
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
                    break;

                default:
                    // @todo - this will be a uuid to be looked up
            }
        }

        return Inertia::render('Comms/MassCommsDashboard', [
            'title' => 'Mass Communications',
            'audiences' => $aud_options,
            'activeAudience' => $active_audience,
            'stats' => $stats,
            'historyFeed' => $history_log
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

        if (!empty($templates_model)) {
            $templates = $templates_model//->with('location')->with('detailsDesc')
            ->filter(request()->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Comms/Emails/Templates/EmailTemplatesIndex', [
            'title' => 'Email Templates',
            'filters' => request()->all('search', 'trashed'),
            'templates' => $templates,
        ]);
    }

    public function et_create()
    {
        return Inertia::render('Comms/SMS/Templates/CreateEmailTemplate', [
        ]);
    }

    public function et_edit($id)
    {

        if (!$id) {
            //TODO:flash error
            return Redirect::back();
        }
        return Inertia::render('Comms/SMS/Templates/EditEmailTemplate', [
            'template' => EmailTemplates::find($id)
        ]);
    }

    public function et_store(Request $request)
    {
        $template = $request->validate([
                'name' => 'required',
                'markup' => 'required'
            ]
        );
        dd($template);
//        $template['created_by_user_id'] = $request->user()->id;
//        SmsTemplates::create($template);
        return Redirect::route('comms.email-templates');
    }

    public function et_update(Request $request)
    {
        $template = $request->validate([
                'name' => 'required',
                'markup' => 'required'
            ]
        );
        dd($template);
//        $template['created_by_user_id'] = $request->user()->id;
//        SmsTemplates::create($template);
        return Redirect::route('comms.email-templates');
    }

    public function ec_index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $campaigns = [
            'data' => []
        ];

        $campaigns_model = $this->setupCampaignsObject($is_client_user, 'email', $client_id);

        if (!empty($campaigns_model)) {
            $campaigns = $campaigns_model//->with('location')->with('detailsDesc')
            ->filter(request()->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Comms/Emails/Campaigns/EmailCampaignsIndex', [
            'title' => 'Email Campaigns',
            'filters' => request()->all('search', 'trashed'),
            'campaigns' => $campaigns
        ]);
    }

    public function st_index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $templates = [
            'data' => []
        ];

        $templates_model = $this->setupTemplatesObject($is_client_user, 'sms', $client_id);

        if (!empty($templates_model)) {
            $templates = $templates_model//->with('location')->with('detailsDesc')
            ->filter(request()->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Comms/SMS/Templates/SMSTemplatesIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed'),
            'templates' => $templates,
        ]);
    }

    public function st_create()
    {
        return Inertia::render('Comms/SMS/Templates/CreateSmsTemplate', [
        ]);
    }

    public function st_edit($id)
    {

        if (!$id) {
            //TODO:flash error
            return Redirect::back();
        }
        return Inertia::render('Comms/SMS/Templates/EditSmsTemplate', [
            'template' => SmsTemplates::find($id)
        ]);
    }

    public function st_store(Request $request)
    {
        $template = $request->validate([
                'name' => 'required',
                'markup' => 'required|max:130'
            ]
        );
        dd($template);
//        $template['created_by_user_id'] = $request->user()->id;
//        SmsTemplates::create($template);
        return Redirect::route('comms.sms-templates');
    }

    public function st_update(Request $request)
    {
        $template = $request->validate([
                'name' => 'required',
                'markup' => 'required|max:130'
            ]
        );
        dd($template);
//        $template['created_by_user_id'] = $request->user()->id;
//        SmsTemplates::create($template);
        return Redirect::route('comms.sms-templates');
    }

    public function sc_index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $campaigns = [
            'data' => []
        ];

        $campaigns_model = $this->setupCampaignsObject($is_client_user, 'sms', $client_id);

        if (!empty($campaigns_model)) {
            $campaigns = $campaigns_model//->with('location')->with('detailsDesc')
            ->filter(request()->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Comms/SMS/Campaigns/SmsCampaignsIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed'),
            'campaigns' => $campaigns
        ]);
    }
}
