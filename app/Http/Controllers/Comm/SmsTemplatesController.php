<?php

namespace App\Http\Controllers\Comm;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SmsTemplatesController extends Controller
{
    public function index(): Response
    {
        $page_count = 10;
        $templates = [
            'data' => [],
        ];

        $templates = SmsTemplate::with('creator')
            ->filter(request()->only('search', 'trashed'))
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));


        return Inertia::render('Comms/SMS/Templates/SMSTemplatesIndex', [
            'title' => 'SMS Templates',
            'filters' => request()->all('search', 'trashed'),
            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Comms/SMS/Templates/CreateSmsTemplate', [
        ]);
    }

    public function edit(SmsTemplate $smsTemplate)
    {
        // @todo - need to build access validation here.

        return Inertia::render('Comms/SMS/Templates/EditSmsTemplate', [
            'template' => $smsTemplate,
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(): array
    {
        $client_id = request()->user()->client_id;
        $is_client_user = request()->user()->isClientUser();
        $templates = [
            'data' => [],
        ];

        $templates_model = $this->setupTemplatesObject($is_client_user, 'sms', $client_id);

        if (! empty($templates_model)) {
            $templates = $templates_model//->with('location')->with('detailsDesc')
            ->with('creator')
                ->filter(request()->only('search', 'trashed'))
                ->get();
        }

        return $templates;
    }
}
