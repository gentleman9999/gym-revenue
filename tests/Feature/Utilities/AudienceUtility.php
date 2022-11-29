<?php

declare(strict_types=1);

namespace Tests\Feature\Utilities;

use App\Domain\Audiences\Actions\CreateAudience;
use App\Domain\Audiences\Audience;
use App\Domain\Clients\Projections\Client;

class AudienceUtility
{
    public static function create(array $attribute = []): Audience
    {
        return CreateAudience::run(Audience::factory()->raw([
            'client_id' => Client::factory()->create()->id,
        ]));
    }
}
