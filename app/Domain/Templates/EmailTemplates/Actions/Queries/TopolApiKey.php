<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Actions\Queries;

use Lorisleiva\Actions\Concerns\AsAction;

class TopolApiKey
{
    use AsAction;

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): string
    {
        return $this->handle();
    }

    public function handle(): string
    {
        return env('TOPOL_API_KEY');
    }
}
