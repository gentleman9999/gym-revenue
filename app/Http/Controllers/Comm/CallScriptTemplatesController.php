<?php

namespace App\Http\Controllers\Comm;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CallScriptTemplatesController extends Controller
{
    public function index(): Response
    {
        $page_count = 10;
        $templates = [
            'data' => [],
        ];

        $templates = CallScriptTemplate::with('creator')
                ->filter(request()->only('search', 'trashed'))
                ->whereUseOnce(false)
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));


        return Inertia::render('Comms/CallScripts/Templates/CallScriptTemplatesIndex', [
            'title' => 'callscript Templates',
            'filters' => request()->all('search', 'trashed'),
//            'templates' => $templates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Comms/CallScripts/Templates/CreateCallScriptTemplate', [
            'topolApiKey' => env('TOPOL_API_KEY'),
            'plansUrl' => env('APP_URL') . "/api/plans",
        ]);
    }

    public function edit(CallScriptTemplate $callscriptTemplate): Response
    {
        // @todo - need to build access validation here.

        return Inertia::render('Comms/CallScripts/Templates/EditCallScriptTemplate', [
            'template' => $callscriptTemplate,
            'topolApiKey' => env('TOPOL_API_KEY'),
            'plansUrl' => env('APP_URL') . "/api/plans",
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(): array
    {
        $templates = [
            'data' => [],
        ];

        if (! empty($templates_model)) {
            $templates = CallScriptTemplate::with('creator')
                ->whereUseOnce(false)
                ->filter(request()->only('search', 'trashed'))
                ->get();
        }

        return $templates;
    }
}
