<?php

namespace App\Http\Controllers\Comm;

use App\Aggregates\Clients\ClientAggregate;
use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplateDetails;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\Models\Endusers\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

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
                    ->with('creator')
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
        return Inertia::render('Comms/Emails/Templates/CreateEmailTemplate', []);
    }

    public function et_edit($id)
    {

        if (!$id) {
            Alert::error("No Template ID provided")->flash();
            return Redirect::back();
        }

        $template = EmailTemplates::find($id);
        // @todo - need to build access validation here.

        return Inertia::render('Comms/Emails/Templates/EditEmailTemplate', [
            'template' => $template
        ]);
    }

    public function et_store(Request $request)
    {
        $template = $request->validate([
                'name' => 'required',
                'markup' => 'required'
            ]
        );

        $client_id = request()->user()->currentClientId();
        // @todo - this could come in handy
        //$is_client_user = request()->user()->isClientUser();
        try {
            $template['active'] = '0';
            $template['client_id'] = $client_id;
            $template['created_by_user_id'] = $request->user()->id;
            EmailTemplates::create($template);
            Alert::info("New Template {$template['name']} was created")->flash();
        }
        catch(\Exception $e)
        {
            Alert::error("New Template {$template['name']} could not be created")->flash();
            return redirect()->back();
        }

        return Redirect::route('comms.email-templates');
    }

    public function et_update(Request $request)
    {
        $data = $request->validate([
                'id' => 'required|exists:email_templates,id',
                'name' => 'required',
                'markup' => 'required',
                'active' => 'sometimes',
                'client_id' => 'required|exists:clients,id',
                'created_by_user_id' => 'required',
            ]
        );

        $template = EmailTemplates::find($data['id']);
        $old_values = $template->toArray();
        $template->name = ($template->name == $data['name']) ? $template->name : $data['name'];
        $template->markup = ($template->markup == $data['markup']) ? $template->markup : $data['markup'];
        $template->active = ($template->active == $data['active']) ? $template->active : $data['active'];
        $template->save();

        ClientAggregate::retrieve($data['client_id'])
            ->updateEmailTemplate($template->id, request()->user()->id, $old_values, $template->toArray())
            ->persist();
        /*

        */
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
            ->with('creator')
            ->filter(request()->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Comms/Emails/Campaigns/EmailCampaignsIndex', [
            'title' => 'Email Campaigns',
            'filters' => request()->all('search', 'trashed'),
            'campaigns' => $campaigns
        ]);
    }

    public function ec_create()
    {
        return Inertia::render('Comms/Emails/Campaigns/CreateEmailCampaign', [
        ]);
    }

    public function ec_edit($id)
    {
        if (!$id) {
            Alert::error("No Campaign ID provided")->flash();
            return redirect()->back();
        }

        $campaign = EmailCampaigns::find($id);
        $templates = EmailTemplates::whereClientId($campaign->client_id)
            ->whereActive('1')
            ->get();
        // @todo - need to build access validation here.

        return Inertia::render('Comms/Emails/Campaigns/EditEmailCampaign', [
            'campaign' => $campaign,
            'templates' => $templates
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
            ->with('creator')
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
            Alert::error("No Template ID provided")->flash();
            return Redirect::back();
        }

        $template = SmsTemplates::find($id);
        // @todo - need to build access validation here.

        return Inertia::render('Comms/SMS/Templates/EditSmsTemplate', [
            'template' => $template
        ]);
    }

    public function st_store(Request $request)
    {
        $template = $request->validate([
                'name' => 'required',
                'markup' => 'required|max:130'
            ]
        );
        $client_id = request()->user()->currentClientId();
        // @todo - this could come in handy
        //$is_client_user = request()->user()->isClientUser();
        try {
            $template['active'] = '0';
            $template['client_id'] = $client_id;
            $template['created_by_user_id'] = $request->user()->id;
            SmsTemplates::create($template);
            Alert::info("New Template {$template['name']} was created")->flash();
        }
        catch(\Exception $e)
        {
            Alert::error("New Template {$template['name']} could not be created")->flash();
            return redirect()->back();
        }

//        $template['created_by_user_id'] = $request->user()->id;
//        SmsTemplates::create($template);
        return Redirect::route('comms.sms-templates');
    }

    public function st_update(Request $request)
    {
        $data = $request->validate([
                'id' => 'required|exists:sms_templates,id',
                'name' => 'required',
                'markup' => 'required|max:130',
                'active' => 'sometimes',
                'client_id' => 'required|exists:clients,id',
                'created_by_user_id' => 'required',
            ]
        );

        $template = SmsTemplates::find($data['id']);
        $old_values = $template->toArray();
        $template->name = ($template->name == $data['name']) ? $template->name : $data['name'];
        $template->markup = ($template->markup == $data['markup']) ? $template->markup : $data['markup'];
        $template->active = ($template->active == $data['active']) ? $template->active : $data['active'];
        $template->save();

        ClientAggregate::retrieve($data['client_id'])
            ->updateSmsTemplate($template->id, request()->user()->id, $old_values, $template->toArray())
            ->persist();
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
            ->with('creator')
            ->filter(request()->only('search', 'trashed'))
                ->paginate($page_count);
        }

        return Inertia::render('Comms/SMS/Campaigns/SmsCampaignsIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed'),
            'campaigns' => $campaigns
        ]);
    }

    public function sc_create()
    {
        return Inertia::render('Comms/SMS/Campaigns/CreateSmsCampaign', [
        ]);
    }

    public function sc_edit($id)
    {
        if (!$id) {
            Alert::error("No Campaign ID provided")->flash();
            return redirect()->back();
        }

        $campaign = SmsCampaigns::find($id);
        $templates = SmsTemplates::whereClientId($campaign->client_id)
            ->whereActive('1')
            ->get();
        // @todo - need to build access validation here.

        return Inertia::render('Comms/SMS/Campaigns/EditSmsCampaign', [
            'campaign' => $campaign,
            'templates' => $templates
        ]);
    }

    public function sc_store()
    {
        $template = request()->validate([
                'name' => 'required',
            ]
        );
        $client_id = request()->user()->currentClientId();

        try {
            $template['active'] = '0';
            $template['client_id'] = $client_id;
            $template['created_by_user_id'] = request()->user()->id;
            $new_campaign = SmsCampaigns::create($template);
            Alert::info("New Campaigns {$template['name']} was created")->flash();
        }
        catch(\Exception $e)
        {
            Alert::error("New Template {$template['name']} could not be created")->flash();
            return redirect()->back();
        }

        return Redirect::route('comms.sms-campaigns.edit', ['id' => $new_campaign->id]);
    }

    public function sc_update()
    {

    }
}
