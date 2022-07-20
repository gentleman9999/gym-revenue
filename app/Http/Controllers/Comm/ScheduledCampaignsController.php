<?php

namespace App\Http\Controllers\Comm;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\ScheduledCampaigns\ScheduledCampaign;
use App\Http\Controllers\Controller;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
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
                'entity' => EmailTemplates::class,
                'name' => 'Email',
            ],
            [
                'entity' => SmsTemplates::class,
                'name' => 'SMS',
            ],
        ];

        return Inertia::render('Comms/ScheduledCampaigns/CreateScheduledCampaign', [
            'audiences' => Audience::get(),
            'emailTemplates' => EmailTemplates::whereClientId(request()->user()->currentClientId())->get(),
            'smsTemplates' => SmsTemplates::whereClientId(request()->user()->currentClientId())->get(),
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
                'entity' => EmailTemplates::class,
                'name' => 'Email',
            ],
            [
                'entity' => SmsTemplates::class,
                'name' => 'SMS',
            ],
        ];

        return Inertia::render('Comms/ScheduledCampaigns/EditScheduledCampaign', [
            'scheduledCampaign' => $scheduledCampaign,
            'audiences' => Audience::whereClientId(request()->user()->currentClientId())->get(),
            'emailTemplates' => EmailTemplates::whereClientId(request()->user()->currentClientId())->get(),
            'smsTemplates' => SmsTemplates::whereClientId(request()->user()->currentClientId())->get(),
            'template_types' => $templateTypes,
        ]);
    }

    public function export()
    {
        return ScheduledCampaign::filter(request()->only('search', 'trashed'))->get();
    }
}