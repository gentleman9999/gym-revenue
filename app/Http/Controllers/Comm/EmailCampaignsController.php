<?php

namespace App\Http\Controllers\Comm;

use App\Aggregates\Clients\ClientAggregate;
use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class EmailCampaignsController extends Controller
{
    private function setupCampaignsObject(bool $is_client_user, string $type, string $client_id = null)
    {
        $results = [];

        if ((! is_null($client_id))) {
            // Get the current client or its cape and bay
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::find($client_id);

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
            if (! $is_client_user) {
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

    public function index()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

        $page_count = 10;
        $campaigns = [
            'data' => [],
        ];

        $campaigns_model = $this->setupCampaignsObject($is_client_user, 'email', $client_id);

        if (! empty($campaigns_model)) {
            $campaigns = $campaigns_model//->with('location')->with('detailsDesc')
            ->with('creator')
                ->with('schedule_date')
            ->filter(request()->only('search', 'trashed'))
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }

        return Inertia::render('Comms/Emails/Campaigns/EmailCampaignsIndex', [
            'title' => 'Email Campaigns',
            'filters' => request()->all('search', 'trashed'),
            'campaigns' => $campaigns,
        ]);
    }

    public function create()
    {
        return Inertia::render('Comms/Emails/Campaigns/CreateEmailCampaign', [
        ]);
    }

    public function edit($id)
    {
        if (! $id) {
            Alert::error("No Campaign ID provided")->flash();

            return redirect()->back();
        }

        $campaign = EmailCampaigns::whereId($id)
            ->with('assigned_template')->with('assigned_audience')
            ->with('schedule')->with('schedule_date')
            ->first();
        if ($campaign->schedule_date && strtotime($campaign->schedule_date->value) <= strtotime('now')) {
            Alert::error("{$campaign->name} cannot be edited since it has already launched.")->flash();

            return Redirect::route('comms.email-campaigns');
        }

        $templates = EmailTemplates::whereClientId($campaign->client_id)->whereActive('1')->get();
        $audiences = CommAudience::whereClientId($campaign->client_id)->whereActive('1')->get();
        // @todo - need to build access validation here.
//        dd($campaign->schedule_date->toArray());
        return Inertia::render('Comms/Emails/Campaigns/EditEmailCampaign', [
            'campaign' => $campaign,
            'audiences' => $audiences,
            'emailTemplates' => $templates,
            'availableAudiences' => CommAudience::whereClientId($campaign->client_id)->get(),
            'availableEmailTemplates' => EmailTemplates::whereClientId($campaign->client_id)->get(),
            //'assigned-template' => (!is_null($campaign->assigned_template)) ? $campaign->assigned_template->value : '',
            //'assigned-audience' => (!is_null($campaign->assigned_audience)) ? $campaign->assigned_audience : ''
        ]);
    }

    public function store()
    {
        $template = request()->validate(
            [
                'name' => 'required',
            ]
        );
        $client_id = request()->user()->currentClientId();

        try {
            $template['active'] = '0';
            $template['client_id'] = $client_id;
            $template['created_by_user_id'] = request()->user()->id;
            $new_campaign = EmailCampaigns::create($template);
            Alert::info("New Campaigns {$template['name']} was created")->flash();
        } catch (\Exception $e) {
            Alert::error("New Template {$template['name']} could not be created")->flash();

            return redirect()->back();
        }

        return Redirect::route('comms.email-campaigns.edit', ['id' => $new_campaign->id]);
    }

    public function update($id)
    {
        $data = request()->validate(
            [
                'id' => 'required|exists:email_campaigns,id',
                'name' => 'required',
                'active' => 'sometimes|bool',
                'schedule_date' => 'sometimes',
                'schedule' => 'sometimes',
                'email_templates' => ['sometimes', 'array'],
                'audiences' => ['sometimes', 'array'],
                'client_id' => 'required|exists:clients,id',
                'created_by_user_id' => 'required',
            ]
        );

        $client_aggy = ClientAggregate::retrieve($data['client_id']);
        $campaign = EmailCampaigns::whereId($data['id'])
            ->with('assigned_template')->with('assigned_audience')
            ->with('schedule')->with('schedule_date')
            ->first();
        $old_values = $campaign->toArray();

        // just to start if name is different, change and use aggy to update log
        if ($old_values['name'] != $data['name']) {
            $campaign->name = $data['name'];
            $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id, 'name', $data['name'], $old_values['name']);
            // @todo - log this in user_details in projector
            $campaign->save();
        }

        if ($data['active'] && ($old_values['schedule_date'] === null || strtotime('now') < strtotime($old_values['schedule_date']['value']))) {
            // logic to activate the campaign, or update stuff if it is already so
//            collect($data)->each(function($item, $key) use($old_values, $data, $client_aggy, $campaign){
//                $old_value = '';
//                if(array_key_exists($key, $old_values)){
//                    $old_value = $old_values[$key];
//                }
//                if(is_array($old_value) && array_key_exists( 'value', $old_value)){
//                    $old_value=$old_value['value'];
//                }
//                if($old_value===1){
//                    $old_value=true;
//                }
//                if(!empty($old_value) && $item !== $old_value){
//                    dd(['key'=>$key,'old'=>$old_value, 'new' => $data[$key]]);
//                    $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id,$key,  $data[$key], $old_value);
//
//                }
//            });
            $client_aggy->persist();
            if (is_null($old_values['schedule']) || ($old_values['schedule']['value'] != $data['schedule'])) {
//                $campaign->schedule = $data['schedule'];
                $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id, 'schedule',  $data['schedule'], $old_values['schedule']->value ?? '');
                // @todo - log this in user_details in projector
                $campaign->save();
            } else {
            }

            if ($data['schedule_date'] == 'now') {
                $fire_date = date('Y-m-d H:i:s');
            } else {
//                $fire_date = date('Y-m-d H:i:s', strtotime('now +'.$data['schedule_date']));
                $fire_date = date('Y-m-d H:i:s', strtotime($data['schedule_date']));
            }
            if ((is_null($old_values['schedule_date']) ? '' : $old_values['schedule_date']['value']) != $data['schedule_date']) {
                $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id, 'schedule_date',  $fire_date, $old_values['schedule_date']['value'] ?? '');
                // @todo - log this in user_details in projector
//                $campaign->schedule = $fire_date;
//                $campaign->save();
            }

            if (! is_null($campaign->assigned_audience)) {
                if (collect($campaign->assigned_audience)->isNotEmpty()) {
                    foreach ($campaign->assigned_audience as $assigned_audience) {
                        if (! in_array($assigned_audience->value, $data['audiences'])) {
                            $assigned_audience->active = 0;
                            $assigned_audience->save();
                            $client_aggy = $client_aggy->unassignAudienceFromEmailCampaign($assigned_audience->value, $campaign->id, request()->user()->id);
                        }
                    }
                }
            }
            $client_aggy = $client_aggy->assignAudienceToEmailCampaign($data['audiences'], $campaign->id, request()->user()->id);

            if (! is_null($campaign->assigned_template)) {
                if (collect($campaign->assigned_template)->isNotEmpty()) {
                    foreach ($campaign->assigned_template as $assigned_template) {
                        if (! in_array($assigned_template->value, $data['audiences'])) {
                            $assigned_template->active = 0;
                            $assigned_template->save();
                            $client_aggy = $client_aggy->unassignEmailTemplateFromCampaign($assigned_template->value, $campaign->id, request()->user()->id);
                        }
                    }
                }
            } else {
                if ($old_values['schedule_date'] !== null && strtotime($old_values['schedule_date']['value']) < strtotime('now')) {
                    Alert::error("Campaign {$old_values['name']} was not updated. Schedule Date is in the past")->flash();
                }
            }
            $client_aggy = $client_aggy->assignEmailTemplateToCampaign($data['email_templates'], $campaign->id, request()->user()->id);


            // active = 1 save() with aggy launchCampaign event
            $campaign->active = 1;
            $campaign->save();

            try {
                $client_aggy = $client_aggy->launchEmailCampaign($campaign->id, $fire_date, request()->user()->id);
                $client_aggy->persist();
                Alert::success("Campaign {$campaign->name} has been updated")->flash();
                Alert::success("Campaign {$campaign->name} is now active!")->flash();
            } catch (\Exception $e) {
                // @todo - send an Alert flash indicting the fail.
                // @todo - revert all the changes made
                Alert::error("Campaign {$old_values['name']} encountered")->flash();
                Alert::error("Campaign {$old_values['name']} was not updated")->flash();
                Alert::warning("Campaign {$old_values['name']} data was reverted and not active.")->flash();
                if (App::environment(['local', 'dev'])) {
                    dd($e);
                }
            }


            return Redirect::route('comms.email-campaigns');
        } else {
            // unset schedule and schedule_date if set and use aggy to update logs
            if (! is_null($campaign->schedule)) {
//                $campaign->schedule = null;
                $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id, 'schedule', $data['schedule'], $old_values['schedule']['value'] ?? '');
                // @todo - log this in user_details in projector
//                $campaign->save();
            }

            // unset schedule_date if set and use aggy to update logs or skip
            if (! is_null($campaign->schedule_date)) {
//                $campaign->schedule_date = null;
                $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id, 'schedule_date',  $data['schedule_date'], $old_values['schedule_date']['value'] ?? '');
                // @todo - log this in user_details in projector
//                $campaign->save();
            }

            // @todo - if audience_id is not null, set it in campaign_details and use aggy to update logs in Client and Audiences and User
            if (array_key_exists('audience_id', $data)) {
                if (! is_null($data['audiences'])) {
                    if ($old_values['active'] == 1) {
                        // this means its getting shut off so
                        if (! is_null($campaign->assigned_audience)) {
                            $campaign->assigned_audience->active = 0;
                            $campaign->assigned_audience->save();
                            $client_aggy = $client_aggy->unassignAudienceFromEmailCampaign($campaign->assigned_audience->value, $campaign->id, request()->user()->id);
                        }
                    } else {
                        // it was never active so it's okay to assign a template if it wasn't already
                        if (! is_null($campaign->assigned_audience)) {
                            $campaign->assigned_audience->active = 0;
                            $campaign->assigned_audience->save();

                            if (($campaign->assigned_audience->value != $data['audience_id'])) {
                                $client_aggy = $client_aggy->unassignAudienceFromEmailCampaign($campaign->assigned_audience->value, $campaign->id, request()->user()->id);

                                if (! is_null($data['audience_id'])) {
                                    $client_aggy = $client_aggy->assignAudienceToEmailCampaign($data['audiences'], $campaign->id, request()->user()->id);
                                }
                            } else {
                                $client_aggy = $client_aggy->unassignAudienceFromEmailCampaign($campaign->assigned_audience->value, $campaign->id, request()->user()->id);
                            }
                        } else {
                            $client_aggy = $client_aggy->assignAudienceToEmailCampaign($data['audiences'], $campaign->id, request()->user()->id);
                        }
                    }
                } else {
                    if (! is_null($campaign->assigned_audience)) {
                        $campaign->assigned_audience->active = 0;
                        $campaign->assigned_audience->save();
                        $client_aggy->unassignAudienceFromEmailCampaign($campaign->assigned_audience->value, $campaign->id, request()->user()->id);
                    }
                }
            }

            // @todo - if email_template_id is not null, set it in campaign_details and use aggy to update logs in Client and Templates and User
            if (array_key_exists('email_template_id', $data)) {
                if ((! is_null($data['email_template_id']))) {
                    if ($old_values['active'] == 1) {
                        // this means its getting shut off so
                        if (! is_null($campaign->assigned_template)) {
                            $campaign->assigned_template->active = 0;
                            $campaign->assigned_template->save();
                            $client_aggy = $client_aggy->unassignEmailTemplateFromCampaign($campaign->assigned_template->value, $campaign->id, request()->user()->id);
                        }
                    } else {
                        // it was never active so it's okay to assign a template if it wasn't already
                        if (! is_null($campaign->assigned_template)) {
                            $campaign->assigned_template->active = 0;
                            $campaign->assigned_template->save();

                            if (($campaign->assigned_template->value != $data['email_template_id'])) {
                                $client_aggy = $client_aggy->unassignEmailTemplateFromCampaign($campaign->assigned_template->value, $campaign->id, request()->user()->id);

                                if (! is_null($data['email_template_id'])) {
                                    $client_aggy = $client_aggy->assignEmailTemplateToCampaign($data['email_template_id'], $campaign->id, request()->user()->id);
                                }
                            } else {
                                $client_aggy = $client_aggy->unassignEmailTemplateFromCampaign($campaign->assigned_template->value, $campaign->id, request()->user()->id);
                            }
                        } else {
                            $client_aggy = $client_aggy->assignEmailTemplateToCampaign($data['email_template_id'], $campaign->id, request()->user()->id);
                        }
                    }
                } else {
                    if (! is_null($campaign->assigned_template)) {
                        $campaign->assigned_template->active = 0;
                        $campaign->assigned_template->save();
                        $client_aggy->unassignEmailTemplateFromCampaign($campaign->assigned_template->value, $campaign->id, request()->user()->id);
                    }
                }
            }

            // @todo - if active == 1, set it to 0 and use aggy to update logs in Client and User
            if ($campaign->active == 1) {
                $client_aggy = $client_aggy->updateEmailCampaign($campaign->id, request()->user()->id, 'active',  false, true);
                $campaign->active = 0;
                $campaign->save();
            }

            // @todo - make details relations
            // @todo - if ttl, details record, active == 0 and softdelete and use aggy to update logs in Client and User
            $client_aggy->persist();
            Alert::success("Campaign {$campaign->name} has been updated")->flash();
            if (! $data['active']) {
                Alert::warning("Campaign {$campaign->name} is not active.")->flash();
            }

            return Redirect::route('comms.email-campaigns.edit', $id);
        }
    }

    public function trash($id)
    {
        if (! $id) {
            Alert::error("No Campaign ID provided")->flash();

            return Redirect::back();
        }

        $campaign = EmailCampaigns::findOrFail($id);
        $success = $campaign->deleteOrFail();
        Alert::success("Campaign '{$campaign->name}' trashed")->flash();


        return Redirect::back();
    }

    public function restore(Request $request, $id)
    {
        if (! $id) {
            Alert::error("No Campaign ID provided")->flash();

            return Redirect::back();
        }
        $campaign = EmailCampaigns::withTrashed()->findOrFail($id);
        $campaign->restore();

        Alert::success("Campaign '{$campaign->name}' restored")->flash();

        return Redirect::back();
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export()
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();
        $campaigns = [
            'data' => [],
        ];

        $campaigns_model = $this->setupCampaignsObject($is_client_user, 'email', $client_id);

        if (! empty($campaigns_model)) {
            $campaigns = $campaigns_model//->with('location')->with('detailsDesc')
            ->with('creator')
                ->with('schedule_date')
                ->filter(request()->only('search', 'trashed'))
                ->get();
        }

        return $campaigns;
    }
}
