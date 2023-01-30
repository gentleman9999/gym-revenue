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
        $templates = [
            'data' => [],
        ];

        if (! empty($templates_model)) {
            $templates = SmsTemplate::with('creator')
                ->filter(request()->only('search', 'trashed'))
                ->get();
        }

        return $templates;
    }
}
