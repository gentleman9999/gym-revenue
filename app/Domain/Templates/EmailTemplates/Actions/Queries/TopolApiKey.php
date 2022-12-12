<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Actions\Queries;

use App\Actions\GymRevAction;

class TopolApiKey extends GymRevAction
{
    public function handle(): string
    {
        return env('TOPOL_API_KEY');
    }
}
