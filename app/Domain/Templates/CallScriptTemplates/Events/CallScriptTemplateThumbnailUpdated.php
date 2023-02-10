<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Events;

use App\Domain\Templates\CallScriptTemplates\Projections\CallScriptTemplate;
use App\StorableEvents\GymRevCrudEvent;

class CallScriptTemplateThumbnailUpdated extends GymRevCrudEvent
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
        return CallScriptTemplate::class;
    }

    protected function getOperation(): string
    {
        return "THUMBNAIL_UPDATED";
    }
}
