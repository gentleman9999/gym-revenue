<?php

namespace App\Domain\Audiences\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\AudienceAggregate;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAudience
{
    use AsAction;

    public function handle(string $id, array $payload): Audience
    {
        AudienceAggregate::retrieve($id)->update($payload)->persist();

        return Audience::findOrFail($id);
    }
}
