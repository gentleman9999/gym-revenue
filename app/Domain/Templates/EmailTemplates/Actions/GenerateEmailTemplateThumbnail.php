<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Actions;

use App\Domain\Templates\EmailTemplates\EmailTemplateAggregate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Wnx\SidecarBrowsershot\BrowsershotLambda;

class GenerateEmailTemplateThumbnail
{
    use AsAction;

    public function handle($id, $html): EmailTemplate
    {
        $template = EmailTemplate::findOrFail($id);
        if ($template->thumbnail['url'] ?? null) {
            return $template;
        }
        //idea - use static beforeupdate to check if html is diff, and if so, go ahead and set to null the thumbnail.
        //then, we know whether or not we need to regenerate by checking if its null
        $thumbnail = BrowsershotLambda::html($html)
            ->setDelay(3000)//give it time to load
            ->setScreenshotType('jpeg', 90)
            ->screenshot();

        $client_id = $template->client_id;

        $timestamp = Carbon::now()->timestamp;

        $key = "{$client_id}/template_assets/{$id}-{$timestamp}";
        Storage::disk('s3')->put($key, $thumbnail, 'public');
        $url = Storage::disk('s3')->url($key);

        EmailTemplateAggregate::retrieve($template->id)->setThumbnail($key, $url)->persist();

        return EmailTemplate::findOrFail($id);
    }
}
