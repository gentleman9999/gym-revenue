<?php

namespace App\Http\Controllers\Comm;

use App\Aggregates\Clients\ClientAggregate;
use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplateDetails;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\Models\Endusers\Lead;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;
use function Aws\filter;

class SmsTemplatesController extends Controller
{
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

        $page_count = 10;
        $templates = [
            'data' => []
        ];

        $templates_model = $this->setupTemplatesObject($is_client_user, 'sms', $client_id);

        if (!empty($templates_model)) {
            $templates = $templates_model//->with('location')->with('detailsDesc')
            ->with('creator')
            ->filter(request()->only('search', 'trashed'))
                ->sort()
                ->paginate($page_count);
        }

        return Inertia::render('Comms/SMS/Templates/SMSTemplatesIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed'),
            'templates' => $templates,
        ]);
    }

    public function create()
    {
        return Inertia::render('Comms/SMS/Templates/CreateSmsTemplate', [
        ]);
    }

    public function edit($id)
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

    public function store(Request $request)
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
            $new_template = SmsTemplates::create($template);
            Alert::info("New Template {$template['name']} was created")->flash();
        }
        catch(\Exception $e)
        {
            Alert::error("New Template {$template['name']} could not be created")->flash();
            return redirect()->back();
        }

//        $template['created_by_user_id'] = $request->user()->id;
//        SmsTemplates::create($template);
//        return Redirect::route('comms.sms-templates');
        return Redirect::route('comms.sms-templates.edit', ['id' => $new_template->id]);

    }

    public function update(Request $request)
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

    public function trash($id)
    {
        if (!$id) {
            Alert::error("No Template ID provided")->flash();
            return Redirect::back();
        }

        $template = SmsTemplates::findOrFail($id);
        $success = $template->deleteOrFail();
        Alert::success("Template '{$template->name}' trashed")->flash();


        return Redirect::back();
    }

    public function restore(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Template ID provided")->flash();
            return Redirect::back();
        }
        $template = SmsTemplates::withTrashed()->findOrFail($id);
        $template->restore();

        Alert::success("Template '{$template->name}' restored")->flash();

        return Redirect::back();
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();
        $templates = [
            'data' => []
        ];

        $templates_model = $this->setupTemplatesObject($is_client_user, 'sms', $client_id);

        if (!empty($templates_model)) {
            $templates = $templates_model//->with('location')->with('detailsDesc')
            ->with('creator')
                ->filter(request()->only('search', 'trashed'))
                ->get();
        }

        return $templates;
    }


}
