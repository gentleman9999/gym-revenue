<?php

namespace App\Domain\Audiences\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAudience
{
    use AsAction;

    public function handle(Audience $audience, array $payload): Audience
    {
        AudienceAggregate::retrieve($audience->id)->update($payload)->persist();

        return $audience->refresh();
    }
}
