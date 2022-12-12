<?php

namespace App\Http\Controllers\Comm;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplatesController extends Controller
{
    public function index(): Response
    {
        $page_count = 10;
        $templates = [
            'data' => [],
        ];

        $templates = EmailTemplate::with('creator')
                ->filter(request()->only('search', 'trashed'))
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));


        return Inertia::render('Comms/Emails/Templates/EmailTemplatesIndex', [
            'title' => 'Email Templates',
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Comms/Emails/Templates/CreateEmailTemplate', [
            'topolApiKey' => env('TOPOL_API_KEY'),
            'plansUrl' => env('APP_URL') . "/api/plans",
        ]);
    }

    public function edit(EmailTemplate $emailTemplate): Response
    {
        // @todo - need to build access validation here.

        return Inertia::render('Comms/Emails/Templates/EditEmailTemplate', [
            'template' => $emailTemplate,
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
            $templates = EmailTemplate::with('creator')
                ->filter(request()->only('search', 'trashed'))
                ->get();
        }

        return $templates;
    }

    public function getFiles(): Collection
    {
        $files = File::where('extension', '=', 'jpg')
            ->orWhere('extension', '=', 'png')
            ->get();

        return $files;
    }
}
