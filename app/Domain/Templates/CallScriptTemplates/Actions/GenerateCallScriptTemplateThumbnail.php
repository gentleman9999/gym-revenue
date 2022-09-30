<?php

namespace App\Domain\Templates\CallScriptTemplates\Actions;

use App\Domain\Templates\CallScriptTemplates\CallScriptTemplateAggregate;
use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Wnx\SidecarBrowsershot\BrowsershotLambda;

class GenerateCallScriptTemplateThumbnail
{
    use AsAction;

    public function handle($id, $html): CallScriptTemplate
    {
        $template = CallScriptTemplate::findOrFail($id);
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

        CallScriptTemplateAggregate::retrieve($template->id)->setThumbnail($key, $url)->persist();

        return CallScriptTemplate::findOrFail($id);
    }
}
