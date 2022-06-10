<?php

namespace App\Actions\Clients\Activity\Comms;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Comms\EmailTemplates;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Browsershot\Browsershot;

class GenerateEmailTemplateThumbnail
{
    use AsAction;

    public function handle($id, $html)
    {
        $template = EmailTemplates::findOrFail($id);
        if ($template->thumbnail !== null) {
            return;
        }
        //idea - use static beforeupdate to check if html is diff, and if so, go ahead and set to null the thumbnail.
        //then, we know whether or not we need to regenerate by checking if its null
        $thumbnail = Browsershot::html($html)
            ->setDelay(3000)//give it time to load
            ->setScreenshotType('jpeg', 90)
            ->screenshot();

        $client_id = $template->client_id;
        $key = "{$client_id}/template_assets/{$id}";
        Storage::disk('s3')->put($key, $thumbnail, 'public');
        $url = Storage::disk('s3')->url($key);


        ClientAggregate::retrieve($client_id)
            ->setEmailTemplateThumbnail($id, $url)
            ->persist();

        return EmailTemplates::findOrFail($id);
    }
}
