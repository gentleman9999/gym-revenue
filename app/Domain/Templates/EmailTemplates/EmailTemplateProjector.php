<?php

namespace App\Domain\Templates\EmailTemplates;

use App\Domain\Templates\EmailTemplates\Events\EmailTemplateCreated;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateDeleted;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateRestored;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateThumbnailUpdated;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateTrashed;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateUpdated;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\Domain\Templates\EmailTemplates\Projections\EmailTemplateDetails;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EmailTemplateProjector extends Projector
{
    public function onStartingEventReplay()
    {
        EmailTemplate::truncate();
    }

    public function onEmailTemplateCreated(EmailTemplateCreated $event)
    {
        DB::transaction(function () use ($event) {
            $template_data = array_filter($event->payload, function ($key) {
                return in_array($key, (new EmailTemplate())->getFillable());
            }, ARRAY_FILTER_USE_KEY);

            $template_data['created_by_user_id'] = $event->modifiedBy();
            $template_data['active'] = 0;//TODO: do we really need to set template to inactive? prob only campaign

            $template = new EmailTemplate();
            $template->id = $event->aggregateRootUuid();
            $template->client_id = $event->clientId();
            $template->fill($template_data);
            $template->writeable()->save();

            $msg = 'Template was auto-generated';
            if (! $event->autoGenerated()) {
                $user = User::find($event->userId());
                $msg = 'Template was created by ' . $user->name . ' on ' . $event->createdAt()->format('Y-m-d');
            }
            EmailTemplateDetails::createOrUpdateRecord($event->aggregateRootUuid(), 'created', $event->createdAt(), ['msg' => $msg]);
        });
    }

    public function onEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        DB::transaction(function () use ($event) {
            EmailTemplate::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
            if (! $event->autoGenerated()) {
                $user = User::find($event->modifiedBy());
                $msg = 'Template was updated by ' . $user->name . ' on ' . $event->createdAt()->format('Y-m-d');
                EmailTemplateDetails::createOrUpdateRecord($event->aggregateRootUuid(), 'updated', $event->modifiedBy(), ['msg' => $msg]);
            }
        });
    }

    public function onEmailTemplateTrashed(EmailTemplateTrashed $event): void
    {
        DB::transaction(function () use ($event) {
            EmailTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
            EmailTemplateDetails::withTrashed()->whereEmailTemplateId($event->aggregateRootUuid())->delete();
        });
    }

    public function onEmailTemplateRestored(EmailTemplateRestored $event): void
    {
        DB::transaction(function () use ($data, $event) {
            EmailTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
            EmailTemplateDetails::withTrashed()->whereEmailTemplateId($event->aggregateRootUuid())->restore();
        });
    }

    public function onEmailTemplateDeleted(EmailTemplateDeleted $event): void
    {
        DB::transaction(function () use ($event) {
            EmailTemplate::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
            EmailTemplateDetails::withTrashed()->whereEmailTemplateId($event->aggregateRootUuid())->forceDelete();
        });
    }

    public function onEmailTemplateThumbnailUpdated(EmailTemplateThumbnailUpdated $event)
    {
        EmailTemplate::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail(['thumbnail' => ['key' => $event->key, 'url' => $event->url]]);
    }
}
