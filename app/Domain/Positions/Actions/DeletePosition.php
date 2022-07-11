<?php

namespace App\Domain\Positions\Actions;

use App\Domain\Positions\PositionAggregate;
use App\Models\Position;
use Lorisleiva\Actions\Concerns\AsAction;

class DeletePosition
{
    use AsAction;

    public string $commandSignature = 'audience:delete {id}';
    public string $commandDescription = 'Permanently deletes the Audience';

    public function handle(string $id): Position
    {
        $position = Position::withTrashed()->findOrFail($id);
        PositionAggregate::retrieve($id)->delete()->persist();

        return $position;
    }
}
