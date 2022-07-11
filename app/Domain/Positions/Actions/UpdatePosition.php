<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Audiences\Audience;
use App\Domain\Audiences\PositionAggregate;
use App\Models\Position;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePosition
{
    use AsAction;

    public function handle(string $id, array $payload): Audience
    {
        PositionAggregate::retrieve($id)->update($payload)->persist();

        return Position::findOrFail($id);
    }
}
