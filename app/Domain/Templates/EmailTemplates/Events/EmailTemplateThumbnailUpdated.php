<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Events;

use App\Domain\Templates\EmailTemplates\Projections\EmailTemplate;
use App\StorableEvents\GymRevCrudEvent;

class EmailTemplateThumbnailUpdated extends GymRevCrudEvent
{
    public string $key;
    public string $url;

    public function __construct(string $key, string $url)
    {
        parent::__construct();
        $this->key = $key;
        $this->url = $url;
    }

    public function getEntity(): string
    {
        return EmailTemplate::class;
    }

    protected function getOperation(): string
    {
        return "THUMBNAIL_UPDATED";
    }
}
