<?php

namespace App\Projectors\Clients;

use App\Models\Comms\EmailTemplateDetails;
use App\Models\Comms\EmailTemplates;
use App\Models\User;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateThumbnailUpdated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailTemplatesProjector extends Projector
{
    public function onEmailTemplateCreated(EmailTemplateCreated $event)
    {
        $template_data = array_filter($event->data, function ($key) {
            return in_array($key, (new EmailTemplates())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $template_data['created_by_user_id'] = $event->user;
        $template_data['active'] = 0;//TODO: do we really need to set template to inactive? prob only campaign

        $template = EmailTemplates::create($template_data);

        $msg = 'Template was auto-generated';
        if ($event->user !== 'Auto Generated') {
            $user = User::find($event->user);
            $msg = 'Template was created by ' . $user->name . ' on ' . $event->metaData()['created-at']->format('Y-m-d');
        }
        $detail = EmailTemplateDetails::create([
            'email_template_id' => $event->data['id'],
            'client_id' => $event->client,
            'detail' => 'created',
            'value' => $event->metaData()['created-at'],
            'misc' => ['msg' => $msg],
        ]);

        // also set the email provider gateway slug
        EmailTemplateDetails::create([
            'email_template_id' => $event->data['id'],
            'client_id' => $event->client,
            'detail' => 'email_gateway',
            'value' => 'default_cnb',
            'misc' => ['msg' => 'The Email Provider was set to CnB Mailgun and will be billed.'],
        ]);
    }

    public function onEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        EmailTemplates::findOrFail($event->data['id'])->updateOrFail($event->data);
        if ($event->user !== 'Auto Generated') {
            $user = User::find($event->user);
            $msg = 'Template was updated by ' . $user->name . ' on ' . $event->metaData()['created-at']->format('Y-m-d');
            EmailTemplateDetails::create([
                'email_template_id' => $event->data['id'],
                'client_id' => $event->client,
                'detail' => 'updated',
                'value' => $event->user,
                'misc' => [
                    'msg' => $msg,
                ],
            ]);
        }
    }

    public function onEmailTemplateThumbnailUpdated(EmailTemplateThumbnailUpdated $event)
    {
        EmailTemplates::findOrFail($event->id)->updateOrFail(['thumbnail' => ['key' => $event->key, 'url' => $event->url]]);
    }
}
