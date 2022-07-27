<?php

namespace App\Http\Controllers\Comm;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ScheduledCampaignsController extends Controller
{
    public function index()
    {
        $page_count = 10;
        $scheduledCampaigns = ScheduledCampaign::filter(request()->only('search', 'trashed'))
            ->sort()
            ->paginate($page_count)
            ->appends(request()->except('page'));

        return Inertia::render('Comms/ScheduledCampaigns/List', [
            'filters' => request()->all('search', 'trashed'),
            'scheduledCampaigns' => $scheduledCampaigns,
        ]);
    }

    public function create()
    {
        $templateTypes = [
            [
                'entity' => EmailTemplate::class,
                'name' => 'Email',
            ],
            [
                'entity' => SmsTemplate::class,
                'name' => 'SMS',
            ],
        ];

        return Inertia::render('Comms/ScheduledCampaigns/CreateScheduledCampaign', [
            'audiences' => Audience::get(),
            'emailTemplates' => EmailTemplate::all(),
            'smsTemplates' => SmsTemplate::all(),
            'template_types' => $templateTypes,
        ]);
    }

    public function edit(ScheduledCampaign $scheduledCampaign)
    {

//        if (strtotime($scheduledCampaign->send_at) <= strtotime('now')) {
//            Alert::error("{$scheduledCampaign->name} cannot be edited since it has already launched.")->flash();
//
//            return Redirect::back();
//        }

        $templateTypes = [
            [
                'entity' => EmailTemplate::class,
                'name' => 'Email',
            ],
            [
                'entity' => SmsTemplate::class,
                'name' => 'SMS',
            ],
        ];

        return Inertia::render('Comms/ScheduledCampaigns/EditScheduledCampaign', [
            'scheduledCampaign' => $scheduledCampaign,
            'audiences' => Audience::all(),
            'emailTemplates' => EmailTemplate::all(),
            'smsTemplates' => SmsTemplate::all(),
            'template_types' => $templateTypes,
        ]);
    }

    public function export()
    {
        return ScheduledCampaign::filter(request()->only('search', 'trashed'))->get();
    }
}
